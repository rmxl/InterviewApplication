package org.anangappara.agoratest.fragments;

import static org.anangappara.agoratest.Constants.API_GET_ASSESSMENT;
import static org.anangappara.agoratest.Constants.API_GET_ASSESSMENT_URL;
import static org.anangappara.agoratest.Constants.API_SCHEDULE_ASSESSMENT;

import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AlertDialog;
import androidx.cardview.widget.CardView;
import androidx.fragment.app.Fragment;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import com.google.android.material.button.MaterialButton;
import com.google.gson.Gson;

import org.anangappara.agoratest.Constants;
import org.anangappara.agoratest.NukeSSLCertificates;
import org.anangappara.agoratest.R;
import org.anangappara.agoratest.activities.ScheduleActivity;
import org.anangappara.agoratest.api.AssessmentStatus;
import org.anangappara.agoratest.api.ScheduleRequest;
import org.anangappara.agoratest.api.UrlResponse;
import org.anangappara.agoratest.data.Globals;

import java.io.IOException;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

/**
 * Fragment for handling the scheduling, joining, and cancellation of assessments.
 */
public class ScheduleFragment extends Fragment
{

    // UI Components
    private CardView newAssessment, scheduledAssessment, instantAssessment;
    private MaterialButton buttonSchedule, buttonReschedule, buttonInstant, buttonJoin, buttonCancel;
    private TextView textDate, textTime;
    private ProgressBar loadingProgress;
    private SwipeRefreshLayout swipeRefreshLayout;

    // Tracking state
    private AssessmentStatus currentStatus;
    private String interviewUrl;

    // Background Handler
    private final Handler handler = new Handler();
    private boolean waitingForAssessment = false;

    private AlertDialog progressDialog;

    private Runnable assessmentRunnable;

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
    }

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState)
    {
        View view = inflater.inflate(R.layout.fragment_schedule, container, false);

        // Initialize all UI components
        initializeViews(view);

        // Fetch initial assessment data
        fetchAssessmentStatus();

        return view;
    }

    /**
     * Initializes all UI components.
     *
     * @param view The root view of the fragment.
     */
    private void initializeViews(View view)
    {
        newAssessment = view.findViewById(R.id.card_new_assessment);
        scheduledAssessment = view.findViewById(R.id.card_scheduled_assessment);
        instantAssessment = view.findViewById(R.id.card_instant_assessment);

        buttonSchedule = view.findViewById(R.id.button_schedule_assessment);
        buttonReschedule = view.findViewById(R.id.button_reschedule_assessment);
        buttonInstant = view.findViewById(R.id.button_instant_assessment);
        buttonJoin = view.findViewById(R.id.button_join_assessment);
        buttonCancel = view.findViewById(R.id.button_cancel_assessment);

        textDate = view.findViewById(R.id.text_scheduled_date);
        textTime = view.findViewById(R.id.text_scheduled_time);

        loadingProgress = view.findViewById(R.id.loading_progress);
        swipeRefreshLayout = view.findViewById(R.id.swipe_refresh);

        // Attach button listeners
        buttonSchedule.setOnClickListener(v -> openScheduleActivity());
        buttonReschedule.setOnClickListener(v -> openRescheduleActivity());
        buttonInstant.setOnClickListener(v -> scheduleInstantAssessment());
        buttonJoin.setOnClickListener(v -> joinMeeting());
        buttonCancel.setOnClickListener(v -> cancelAssessment());

        // Set up swipe refresh listener
        swipeRefreshLayout.setOnRefreshListener(() ->
        {
            swipeRefreshLayout.setRefreshing(true);
            fetchAssessmentStatus();
            fetchAssessmentURL();
        });
    }

    /**
     * Fetches the current assessment status from the server.
     */
    private void fetchAssessmentStatus()
    {
        OkHttpClient client = NukeSSLCertificates.getUnsafeOkHttpClient();
        Request request = new Request.Builder().url(API_GET_ASSESSMENT + Globals.getInstance().getUserName()).get().build();

        client.newCall(request).enqueue(new Callback()
        {
            @Override
            public void onFailure(Call call, IOException e)
            {
                Log.e("API_ERROR", "Request failed: " + e.getMessage());
                getActivity().runOnUiThread(() ->
                {
                    Toast.makeText(getActivity(),
                            "Failed to fetch meeting status", Toast.LENGTH_SHORT).show();
                    updateUIForNoMeeting();
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

                    try
                    {
                        currentStatus = gson.fromJson(jsonResponse, AssessmentStatus.class);
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                        currentStatus = new AssessmentStatus();
                        currentStatus.has_scheduled_meeting = false;
                    }

                    getActivity().runOnUiThread(() ->
                    {
                        if (currentStatus != null && currentStatus.has_scheduled_meeting)
                        {
                            updateUIForScheduledMeeting(currentStatus);
                        }
                        else
                        {
                            updateUIForNoMeeting();
                        }
                    });
                }
                else
                {
                    Log.e("API_ERROR", "Response failed: " + response.code());
                    getActivity().runOnUiThread(() ->
                    {
                        dismissLoading();
                        Toast.makeText(getActivity(),
                                "Error fetching meeting status: " + response.code(), Toast.LENGTH_SHORT).show();
                        updateUIForNoMeeting();
                    });
                }
            }
        });
    }

    /**
     * Fetches the URL for joining the assessment (API call).
     */
    private void fetchAssessmentURL()
    {
        OkHttpClient client = NukeSSLCertificates.getUnsafeOkHttpClient();

        System.out.println("fetching");

        Request request = new Request.Builder().url(API_GET_ASSESSMENT_URL + Globals.getInstance().getUserName()).get().build();

        client.newCall(request).enqueue(new Callback()
        {
            @Override
            public void onFailure(Call call, IOException e)
            {
                Log.e("API_ERROR", "Request failed: " + e.getMessage());
                swipeRefreshLayout.setRefreshing(false);
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
                    UrlResponse urlResponse = gson.fromJson(jsonResponse, UrlResponse.class);

                    Constants.latestAssessmentId = urlResponse.id;

                    interviewUrl = Constants.HOST + "/join-meeting/" + urlResponse.url;

                    System.out.println(interviewUrl);

                    getActivity().runOnUiThread(() ->
                    {
                        updateUIForJoinMeeting();
                    });

                    // Print slots
                }
                else
                {
                    buttonJoin.setEnabled(false);
                    Log.e("API_ERROR", "Response failed: " + response.code());
                    swipeRefreshLayout.setRefreshing(false);
                }
            }
        });
    }

    /**
     * Updates the UI when there are no scheduled meetings.
     */
    private void updateUIForNoMeeting()
    {
        scheduledAssessment.setVisibility(View.GONE);
        newAssessment.setVisibility(View.VISIBLE);
        instantAssessment.setVisibility(View.VISIBLE);

        swipeRefreshLayout.setRefreshing(false);
        loadingProgress.setVisibility(View.GONE);
    }

    /**
     * Updates the UI for a scheduled meeting using the given status data.
     *
     * @param status The current assessment status.
     */
    private void updateUIForScheduledMeeting(AssessmentStatus status)
    {
        newAssessment.setVisibility(View.GONE);
        scheduledAssessment.setVisibility(View.VISIBLE);
        instantAssessment.setVisibility(View.GONE);

        textDate.setText(status.date);
        textTime.setText(status.start_time);

        swipeRefreshLayout.setRefreshing(false);
        loadingProgress.setVisibility(View.GONE);

        Log.d("ScheduleFragment", "Scheduled meeting updated: " + status.toString());
    }

    private void updateUIForJoinMeeting()
    {
        scheduledAssessment.setVisibility(View.VISIBLE);
        newAssessment.setVisibility(View.GONE);
        instantAssessment.setVisibility(View.GONE);

        buttonJoin.setEnabled(true);
        swipeRefreshLayout.setRefreshing(false);
    }

    /**
     * Joins the meeting based on the current `interviewUrl`.
     */
    private void joinMeeting()
    {
        if (interviewUrl != null && !interviewUrl.isEmpty())
        {
            Log.d("ScheduleFragment", "Joining meeting with URL: " + interviewUrl);
            // Handle join meeting logic (e.g., launch intent to video call URL)

            dismissLoadingDialog();

            Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(interviewUrl));
            startActivity(browserIntent);
        }
        else
        {
            Toast.makeText(getContext(), getString(R.string.error_missing_url), Toast.LENGTH_SHORT).show();
        }
    }

    /**
     * Schedules an instant assessment.
     */
    private void scheduleInstantAssessment()
    {
        showLoadingDialog();

        interviewUrl = null;

        Gson gson = new Gson();

        ScheduleRequest data = new ScheduleRequest(Globals.getInstance().getUserName(), null, null, "instant");

        String jsonData = gson.toJson(data);
        System.out.println(jsonData);

        MediaType JSON = MediaType.get("application/json; charset=utf-8");
        RequestBody body = RequestBody.create(jsonData, JSON);

        // Build the request
        OkHttpClient client = NukeSSLCertificates.getUnsafeOkHttpClient();
        Request request = new Request.Builder()
                .url(API_SCHEDULE_ASSESSMENT)
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
                getActivity().runOnUiThread(() ->
                {
                    buttonSchedule.setEnabled(true);
                    dismissLoadingDialog();
                    Toast.makeText(getContext(),
                            "Failed to schedule: " + e.getMessage(), Toast.LENGTH_LONG).show();
                });
            }

            @Override
            public void onResponse(Call call, Response response) throws IOException
            {
                final String responseBody = response.body().string();
                getActivity().runOnUiThread(() ->
                {
                    buttonSchedule.setEnabled(true);
                    if (response.isSuccessful())
                    {
                        Log.d("POST_SUCCESS", "Response: " + responseBody);
                        Toast.makeText(getContext(),
                                "Assessment scheduled successfully!", Toast.LENGTH_LONG).show();

                        waitInstantAssessment();
                    }
                    else
                    {
                        Log.e("POST_ERROR", "Response failed: " + response.code() + " " + responseBody);
                        Toast.makeText(getContext(),
                                "Failed to schedule: " + response.code(), Toast.LENGTH_LONG).show();
                    }
                });
            }
        });
    }


    /**
     * Waits for an instant assessment to be ready.
     */
    private void waitInstantAssessment()
    {
        if (waitingForAssessment)
        {
            return; // Prevent duplicate calls
        }
        waitingForAssessment = true;

        assessmentRunnable = new Runnable()
        {
            @Override
            public void run()
            {
                fetchAssessmentURL();

                // If URL is still empty, keep checking
                if (interviewUrl == null || interviewUrl.isEmpty())
                {
                    System.out.println("retrying");
                    handler.postDelayed(this, 3000);
                }
                else
                {
                    // URL found, join meeting
                    joinMeeting();
                    waitingForAssessment = false;
                }
            }
        };

        handler.postDelayed(assessmentRunnable, 0);
    }

    // Method to cancel waiting
    private void cancelWaitInstantAssessment()
    {
        if (waitingForAssessment && assessmentRunnable != null)
        {
            handler.removeCallbacks(assessmentRunnable); // Cancel the task
            waitingForAssessment = false; // Reset the state
            System.out.println("Handler process cancelled.");
        }
    }


    private void cancelAssessment()
    {
        interviewUrl = null;

        Gson gson = new Gson();

        ScheduleRequest data = new ScheduleRequest(Globals.getInstance().getUserName(), null, null, null);

        String jsonData = gson.toJson(data);
        System.out.println(jsonData);

        MediaType JSON = MediaType.get("application/json; charset=utf-8");
        RequestBody body = RequestBody.create(jsonData, JSON);

        // Build the request
        OkHttpClient client = NukeSSLCertificates.getUnsafeOkHttpClient();
        Request request = new Request.Builder()
                .url(Constants.API_CANCEL_INSTANT_ASSESSMENT)
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
                getActivity().runOnUiThread(() ->
                {
                    buttonSchedule.setEnabled(true);
                    dismissLoadingDialog();
                    Toast.makeText(getContext(),
                            "Failed to schedule: " + e.getMessage(), Toast.LENGTH_LONG).show();
                });
            }

            @Override
            public void onResponse(Call call, Response response) throws IOException
            {
                final String responseBody = response.body().string();
                getActivity().runOnUiThread(() ->
                {
                    buttonSchedule.setEnabled(true);
                    fetchAssessmentStatus();
                    if (response.isSuccessful())
                    {
                        Log.d("POST_SUCCESS", "Response: " + responseBody);
                        Toast.makeText(getContext(),
                                "Assessment scheduled successfully!", Toast.LENGTH_LONG).show();
//                        waitInstantAssessment();
                    }
                    else
                    {
                        Log.e("POST_ERROR", "Response failed: " + response.code() + " " + responseBody);
                        Toast.makeText(getContext(),
                                "Failed to schedule: " + response.code(), Toast.LENGTH_LONG).show();
                    }
                });
            }
        });
    }


    private void openScheduleActivity()
    {
        Intent intent = new Intent(getContext(), ScheduleActivity.class);
        startActivity(intent);
    }

    private void openRescheduleActivity()
    {
        Intent intent = new Intent(getContext(), ScheduleActivity.class);
        intent.putExtra("reschedule", true);
        startActivity(intent);
    }

    private void showLoadingDialog()
    {
        AlertDialog.Builder builder = new AlertDialog.Builder(requireContext());
        LayoutInflater inflater = getLayoutInflater();
        View dialogView = inflater.inflate(R.layout.progress_dialog, null);
        builder.setView(dialogView);
        builder.setCancelable(false);

        progressDialog = builder.create();
        progressDialog.getWindow().setBackgroundDrawableResource(android.R.color.transparent);
        progressDialog.show();

        // Cancel Button Click
        MaterialButton cancelButton = dialogView.findViewById(R.id.button_cancel);
        cancelButton.setOnClickListener(v ->
        {
            if (progressDialog.isShowing())
            {
                progressDialog.dismiss();
            }

            cancelWaitInstantAssessment();
            cancelAssessment();
        });
    }

    private void dismissLoadingDialog()
    {
        if (progressDialog != null && progressDialog.isShowing())
        {
            progressDialog.dismiss();
        }
    }

    /**
     * Displays the loading spinner.
     */
    private void showLoading()
    {
        loadingProgress.setVisibility(View.VISIBLE);
    }

    /**
     * Hides the loading spinner.
     */
    private void dismissLoading()
    {
        loadingProgress.setVisibility(View.GONE);
    }
}