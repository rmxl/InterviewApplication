//LoginActivity.java
package org.anangappara.agoratest.activities;

import static org.anangappara.agoratest.Constants.API_LOGIN;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import org.anangappara.agoratest.Constants;
import org.anangappara.agoratest.NukeSSLCertificates;
import org.anangappara.agoratest.R;
import org.anangappara.agoratest.data.Globals;
import org.json.JSONObject;

import java.io.IOException;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

/**
 * Handles the login functionality for the app.
 */
public class LoginActivity extends AppCompatActivity
{

    // UI Components
    private EditText editTextUsername;
    private EditText editTextPassword;
    private Button buttonLogin;

    // Network Constants
    private static final String TAG = "LoginActivity";
    private static final MediaType JSON = MediaType.get("application/json; charset=utf-8");

    // OkHttpClient instance
    private final OkHttpClient client = NukeSSLCertificates.getUnsafeOkHttpClient();

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        // UI Initialization
        initializeUI();

        // Set up button listener
        buttonLogin.setOnClickListener(v -> handleLoginClick());
    }

    /**
     * Initializes the UI elements and sets up the layout.
     */
    private void initializeUI()
    {
        editTextUsername = findViewById(R.id.edittext_username);
        editTextPassword = findViewById(R.id.edittext_password);
        buttonLogin = findViewById(R.id.button_login);
    }

    /**
     * Handles the login button click event.
     */
    private void handleLoginClick()
    {
        String username = editTextUsername.getText().toString().trim();
        String password = editTextPassword.getText().toString().trim();

        // Validate inputs
        if (username.isEmpty() || password.isEmpty())
        {
//            showToast(getString(R.string.error_empty_credentials));
            showToast("Username and Password cannot be empty");
        }
        else
        {
            login(username, password);
        }
    }

    /**
     * Makes a login request to the server using OkHttp3.
     *
     * @param username The username entered by the user.
     * @param password The password entered by the user.
     */
    private void login(String username, String password)
    {
        // Show progress indicator (if available)
        showProgress();

        try
        {
            // Create login request parameters
            JSONObject loginParams = new JSONObject();
            loginParams.put("username", username);
            loginParams.put("password", password);

            // Create POST request body
            RequestBody requestBody = RequestBody.create(loginParams.toString(), JSON);

            // Create Request object
            Request request = new Request.Builder()
                    .url(API_LOGIN)
                    .post(requestBody)
                    .build();

            // Asynchronous request using OkHttp
            client.newCall(request).enqueue(new Callback()
            {
                @Override
                public void onFailure(Call call, IOException e)
                {
                    // Request failed (e.g., network error)
                    Log.e(TAG, "Login request failed: " + e.getMessage());
                    runOnUiThread(() ->
                    {
                        hideProgress();
//                        showToast(getString(R.string.error_login_failed));
                        showToast("Login request failed");
                    });
                }

                @Override
                public void onResponse(Call call, Response response) throws IOException
                {
                    runOnUiThread(() -> hideProgress());

                    if (!response.isSuccessful())
                    {
                        Log.e(TAG, "Server response error: " + response.code());
                        runOnUiThread(() -> showToast("Login request failed"));
                        return;
                    }

                    // Parse the response
                    try
                    {
                        if (response.body() != null)
                        {
                            String jsonResponse = response.body().string();
                            JSONObject jsonObject = new JSONObject(jsonResponse);

                            Globals.getInstance().setUserId(jsonObject.getJSONObject("user").getInt("id"));
                            Globals.getInstance().setUserName(jsonObject.getJSONObject("user").getString("username"));


                            // Show success message and navigate to MainActivity
                            runOnUiThread(() ->
                            {
//                                showToast(getString(R.string.login_success));
                                showToast("Login successful");
                                navigateToMainActivity();
                            });
                        }
                    }
                    catch (Exception e)
                    {
                        Log.e(TAG, "Error parsing response: " + e.getMessage());
//                        runOnUiThread(() -> showToast(getString(R.string.error_invalid_response)));
                        runOnUiThread(() -> showToast("Error parsing response"));
                    }
                }
            });

        }
        catch (Exception e)
        {
            Log.e(TAG, "Error creating request: " + e.getMessage());
            hideProgress();
//            showToast(getString(R.string.error_login_failed));
            showToast("Login Failed");
        }
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

    /**
     * Navigates to the MainActivity and closes the LoginActivity.
     */
    private void navigateToMainActivity()
    {
        Intent intent = new Intent(LoginActivity.this, MainActivity.class);
        startActivity(intent);
        finish();
    }

    /**
     * Shows a progress indicator (if a ProgressBar is added to the layout).
     */
    private void showProgress()
    {
        // Implement a progress bar visibility if required
        // Example: progressBar.setVisibility(View.VISIBLE);
    }

    /**
     * Hides the progress indicator (if a ProgressBar is added to the layout).
     */
    private void hideProgress()
    {
        // Implement a progress bar hiding logic
        // Example: progressBar.setVisibility(View.GONE);
    }
}