//ScheduleActivity.java
package org.anangappara.agoratest.activities;

import android.app.DatePickerDialog;
import android.os.Bundle;
import android.util.Log;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.google.android.material.button.MaterialButton;
import com.google.android.material.datepicker.CalendarConstraints;
import com.google.android.material.datepicker.CompositeDateValidator;
import com.google.android.material.datepicker.DateValidatorPointBackward;
import com.google.android.material.datepicker.DateValidatorPointForward;
import com.google.android.material.datepicker.MaterialDatePicker;
import com.google.android.material.textfield.MaterialAutoCompleteTextView;
import com.google.android.material.textfield.TextInputEditText;
import com.google.gson.Gson;

import org.anangappara.agoratest.Constants;
import org.anangappara.agoratest.NukeSSLCertificates;
import org.anangappara.agoratest.R;
import org.anangappara.agoratest.api.ScheduleRequest;
import org.anangappara.agoratest.api.TimeSlot;
import org.anangappara.agoratest.api.TimeSlotList;

import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.Locale;
import java.util.TimeZone;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

/**
 * Activity for scheduling or rescheduling appointments.
 */
public class ScheduleActivity extends AppCompatActivity
{

    // UI Elements
    private TextInputEditText textDate;
    private MaterialAutoCompleteTextView dropdownTime;
    private MaterialButton buttonSchedule;
    private MaterialButton buttonDatePicker;
    private TextView textTitle;

    // State Management
    private boolean isReschedule;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_schedule);

        // Initialize UI Components
        initializeUI();

        // check if the user is trying to schedule a new assessment or reschedule existing assessment
        isReschedule = getIntent().getBooleanExtra("reschedule", false);
        updateTitle();

        // Set up the dropdowns for time selection
        setupDropdowns();

        // Set up date picker listener
        buttonDatePicker.setOnClickListener(v -> showDatePicker());

        // Set up schedule button listener
        buttonSchedule.setOnClickListener(v -> schedule());
    }

    /**
     * Initializes the UI components.
     */
    private void initializeUI()
    {
        textDate = findViewById(R.id.text_date);
        dropdownTime = findViewById(R.id.dropdown_time);
        buttonSchedule = findViewById(R.id.button_schedule);
        buttonDatePicker = findViewById(R.id.button_date);
        textTitle = findViewById(R.id.title_schedule);
    }

    /**
     * Updates the title of the activity depending on whether it is a reschedule or new schedule.
     */
    private void updateTitle()
    {
        String title = isReschedule
                ? getString(R.string.reschedule_title)
                : getString(R.string.schedule_title);
        textTitle.setText(title);
    }

    /**
     * Sets up dropdown adapters and other options.
     */
    private void setupDropdowns()
    {
        // Example API call to fetch available time slots for today
        String today = getTodayDate();
        fetchAvailableSlots(today);

        // Pre-fill dropdown with today's date
        textDate.setText(today);
    }

    /**
     * Sets an adapter for a dropdown with a list of items.
     *
     * @param dropdown The dropdown view to configure.
     * @param items    The list of items to display in the dropdown.
     */
    private void setDropdownAdapter(MaterialAutoCompleteTextView dropdown, List<String> items)
    {
        ArrayAdapter<String> adapter = new ArrayAdapter<>(this,
                android.R.layout.simple_dropdown_item_1line, items);
        dropdown.setAdapter(adapter);
    }

    /**
     * Displays a date picker dialog and updates the selected date.
     */
    private void showDatePicker() {
        String currentTextDate = textDate.getText().toString();
        Long parsedDate = null;

        if (!currentTextDate.isEmpty()) {
            try {
                // Ensure the formatter uses local timezone
                SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
                // Parse the current text date
                Date date = sdf.parse(currentTextDate);

                if (date != null) {
                    // Convert the parsed date to UTC timestamp
                    Calendar utcCalendar = Calendar.getInstance(TimeZone.getTimeZone("UTC"));
                    utcCalendar.setTime(date);
                    parsedDate = utcCalendar.getTimeInMillis();
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }

        Calendar calendar = Calendar.getInstance(TimeZone.getTimeZone("UTC"));
        long today = calendar.getTimeInMillis();

        // Set maximum date (7 days from today)
        calendar.add(Calendar.DAY_OF_MONTH, 7);
        long maxDate = calendar.getTimeInMillis();

        ArrayList<CalendarConstraints.DateValidator> validators = new ArrayList<>();
        validators.add(DateValidatorPointForward.from(today));
        validators.add(DateValidatorPointBackward.before(maxDate));

        // Set calendar constraints
        CalendarConstraints constraints = new CalendarConstraints.Builder()
                .setStart(today)
                .setEnd(maxDate)
                .setValidator(CompositeDateValidator.allOf(validators))
                .build();

        // Create and show the MaterialDatePicker
        MaterialDatePicker<Long> datePicker = MaterialDatePicker.Builder.datePicker()
                .setTitleText("Select Interview Date")
                .setCalendarConstraints(constraints)
                .setSelection(parsedDate != null ? parsedDate : today) // Use UTC time
                .build();

        datePicker.show(getSupportFragmentManager(), "DATE_PICKER");

        // Handle the selected date
        datePicker.addOnPositiveButtonClickListener(selection -> {
            // Use UTC timezone for formatting
            SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
            sdf.setTimeZone(TimeZone.getTimeZone("UTC"));

            String formattedDate = sdf.format(new Date(selection));

            textDate.setText(formattedDate);
            Toast.makeText(ScheduleActivity.this, "Selected Date: " + formattedDate, Toast.LENGTH_SHORT).show();

            // Fetch available slots for this date
            fetchAvailableSlots(formattedDate);
        });
    }

    /**
     * Fetches available time slots for the selected date (mock API call or actual network request).
     *
     * @param date The selected date in yyyy-MM-dd format.
     */
    private void fetchAvailableSlots(String date)
    {
        String API_URL = Constants.HOST + "/api/available-slots?date=" + date;
        OkHttpClient client = NukeSSLCertificates.getUnsafeOkHttpClient();

        Log.d("API_REQUEST", "Fetching slots for: " + date);
        Request request = new Request.Builder().url(API_URL).get().build();

        client.newCall(request).enqueue(new Callback()
        {
            @Override
            public void onFailure(Call call, IOException e)
            {
                Log.e("API_ERROR", "Request failed: " + e.getMessage());
                runOnUiThread(() ->
                {
                    Toast.makeText(ScheduleActivity.this,
                            "Failed to fetch available slots", Toast.LENGTH_SHORT).show();
                    updateTimeSlots(new ArrayList<>());
                });
            }

            @Override
            public void onResponse(Call call, Response response) throws IOException
            {
                if (response.isSuccessful())
                {
                    String jsonResponse = response.body().string();
                    Log.d("API_RESPONSE", jsonResponse);

                    // Parse JSON
                    Gson gson = new Gson();
                    TimeSlotList slotList = gson.fromJson(jsonResponse, TimeSlotList.class);
                    List<String> timeSlots = new ArrayList<>();

                    // Extract time slots
                    for (TimeSlot slot : slotList.available_slots)
                    {
                        timeSlots.add(slot.start_time);
                    }

                    runOnUiThread(() -> updateTimeSlots(timeSlots));
                }
                else
                {
                    Log.e("API_ERROR", "Response failed: " + response.code());
                    runOnUiThread(() ->
                    {
                        Toast.makeText(ScheduleActivity.this,
                                "Error fetching slots: " + response.code(), Toast.LENGTH_SHORT).show();
                        updateTimeSlots(new ArrayList<>());
                    });
                }
            }
        });
    }


    /**
     * Updates the dropdown with available time slots.
     *
     * @param timeSlots The list of available time slots.
     */
    private void updateTimeSlots(List<String> timeSlots)
    {
        if (timeSlots.isEmpty())
        {
            timeSlots.add("No Slots Available");
            setDropdownAdapter(dropdownTime, timeSlots);
            dropdownTime.setEnabled(false);
            buttonSchedule.setEnabled(false);
        }
        else
        {
            setDropdownAdapter(dropdownTime, timeSlots);
            dropdownTime.setEnabled(true);
            buttonSchedule.setEnabled(true);

            // Set first item as selected
            if (!timeSlots.isEmpty())
            {
                dropdownTime.setText(timeSlots.get(0), false);
            }
        }
    }

    /**
     * Schedules the appointment based on the selected date and time.
     */
    private void schedule()
    {
        String selectedTime = dropdownTime.getText().toString();
        String selectedDate = textDate.getText().toString();
//        String selectedLanguage = dropdownLanguage.getText().toString();
//        String selectedExperience = dropdownExperience.getText().toString();

        // Validate selections
        if (selectedTime.equals("No Slots Available"))
        {
            Toast.makeText(this, "No available slots for scheduling", Toast.LENGTH_SHORT).show();
            return;
        }

//        if (selectedLanguage.isEmpty()) {
//            Toast.makeText(this, "Please select a language", Toast.LENGTH_SHORT).show();
//            return;
//        }

//        if (selectedExperience.isEmpty()) {
//            Toast.makeText(this, "Please select your experience level", Toast.LENGTH_SHORT).show();
//            return;
//        }

        // Disable button to prevent double submissions
        buttonSchedule.setEnabled(false);

        // Show progress
        Toast.makeText(this, "Scheduling your interview...", Toast.LENGTH_SHORT).show();

        String url;

        // Create request object - modified to include all parameters
//        ScheduleRequest data = new ScheduleRequest(
//                "johndoe",
//                selectedDate,
//                selectedTime);

        // Convert to JSON
        Gson gson = new Gson();
        String jsonData;
        // Log.d("SCHEDULE_REQUEST", jsonData);

        if (isReschedule)
        {
            url = Constants.HOST + "/api/reschedule";  // New backend endpoint for rescheduling
            ScheduleRequest data = new ScheduleRequest("johndoe", selectedDate, selectedTime, "scheduled");
            jsonData = gson.toJson(data);
        }
        else
        {
            url = Constants.HOST + "/api/schedule";
            ScheduleRequest data = new ScheduleRequest("johndoe", selectedDate, selectedTime, "scheduled");
            jsonData = gson.toJson(data);
            System.out.println(jsonData);
        }

        // Create request body
        MediaType JSON = MediaType.get("application/json; charset=utf-8");
        RequestBody body = RequestBody.create(jsonData, JSON);

        // Build the request
        OkHttpClient client = NukeSSLCertificates.getUnsafeOkHttpClient();
        Request request = new Request.Builder()
                .url(url)
                .post(body)
                .addHeader("Content-Type", "application/json")
                .build();

        // Send request
        client.newCall(request).enqueue(new Callback()
        {
            @Override
            public void onFailure(Call call, IOException e)
            {
                Log.e("POST_ERROR", "Request failed: " + e.getMessage());
                runOnUiThread(() -> {
                    buttonSchedule.setEnabled(true);
                    Toast.makeText(ScheduleActivity.this,
                            "Failed to schedule: " + e.getMessage(), Toast.LENGTH_LONG).show();
                });
            }

            @Override
            public void onResponse(Call call, Response response) throws IOException
            {
                final String responseBody = response.body().string();
                runOnUiThread(() -> {
                    buttonSchedule.setEnabled(true);
                    if (response.isSuccessful())
                    {
                        Log.d("POST_SUCCESS", "Response: " + responseBody);
                        Toast.makeText(ScheduleActivity.this,
                                "Assessment scheduled successfully!", Toast.LENGTH_LONG).show();
                        // Navigate back or to confirmation screen
                        finish();
                    }
                    else
                    {
                        Log.e("POST_ERROR", "Response failed: " + response.code() + " " + responseBody);
                        Toast.makeText(ScheduleActivity.this,
                                "Failed to schedule: " + response.code(), Toast.LENGTH_LONG).show();
                    }
                });
            }
        });
    }

    /**
     * Gets today's date in yyyy-MM-dd format.
     *
     * @return The current date in the specified format.
     */
    private String getTodayDate()
    {
        return new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                .format(Calendar.getInstance().getTime());
    }

    /**
     * Shows a toast message to the user.
     *
     * @param message The message to display.
     */
    private void showToast(String message)
    {
        Toast.makeText(this, message, Toast.LENGTH_SHORT).show();
    }
}