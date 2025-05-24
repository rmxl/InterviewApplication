package org.anangappara.agoratest.fragments;
import static org.anangappara.agoratest.Constants.API_USER_INFO;

import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.fragment.app.Fragment;

import com.google.gson.Gson;

import org.anangappara.agoratest.Constants;
import org.anangappara.agoratest.NukeSSLCertificates;
import org.anangappara.agoratest.R;
import org.anangappara.agoratest.api.UserResponse;
import org.anangappara.agoratest.data.Globals;

import java.io.IOException;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.Response;

public class ProfileFragment extends Fragment
{

    TextView textName;
    TextView textJob;
    TextView textGrade;

    LinearLayout containerGrade;

    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState)
    {
        View view = inflater.inflate(R.layout.fragment_profile, container, false);

        initializeViews(view);
        fetchUserDetails();

        return view;
    }

    private void initializeViews(View view)
    {
        textName = view.findViewById(R.id.text_name);
        textJob = view.findViewById(R.id.text_job);
        textGrade = view.findViewById(R.id.text_grade);
        containerGrade = view.findViewById(R.id.container_grade);
    }

    void updateUIForUserDetails(UserResponse response)
    {
        if (response != null)
        {
            textName.setText(response.name != null ? response.name : "N/A");
            textJob.setText(response.job_title != null ? response.job_title : "N/A");
            textGrade.setText(response.rating != -1 ? Float.toString(response.rating) : "N/A");

            if (response.rating >= 9)
            {
                textGrade.setText("EXPERT");
                containerGrade.setBackgroundResource(R.drawable.background_experience_expert);
            }
            else if (response.rating >= 7)
            {
                textGrade.setText("SKILLED");
                containerGrade.setBackgroundResource(R.drawable.background_experience_skilled);
            }
            else if (response.rating >= 3)
            {
                textGrade.setText("NEWBIE");
                containerGrade.setBackgroundResource(R.drawable.background_experience_newbie);
            }
            else
            {
                textGrade.setText("YET TO TAKE ASSESSMENT");
                containerGrade.setBackgroundResource(R.drawable.background_experience_unassessed);
            }
        }
    }


    private void fetchUserDetails()
    {
        String url = API_USER_INFO + Globals.getInstance().getUserId();

        OkHttpClient client = NukeSSLCertificates.getUnsafeOkHttpClient();
        Request request = new Request.Builder().url(url).get().build();

        client.newCall(request).enqueue(new Callback()
        {
            UserResponse userdetails;

            @Override
            public void onFailure(Call call, IOException e)
            {
                Log.e("API_ERROR", "Request failed: " + e.getMessage());
                getActivity().runOnUiThread(() -> {
                    Toast.makeText(getActivity(),
                            "Failed to fetch meeting status", Toast.LENGTH_SHORT).show();
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
                        userdetails = gson.fromJson(jsonResponse, UserResponse.class);
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                        userdetails = new UserResponse();
                    }

                    getActivity().runOnUiThread(() -> {
                        if (userdetails != null)
                        {
                            updateUIForUserDetails(userdetails);
                        }
//                        else
//                        {
////                            updateUIForNoMeeting();
//                        }
                    });
                }
                else
                {
                    Log.e("API_ERROR", "Response failed: " + response.code());
                    getActivity().runOnUiThread(() -> {
                        Toast.makeText(getActivity(),
                                "Error fetching meeting status: " + response.code(), Toast.LENGTH_SHORT).show();
                    });
                }
            }
        });
    }
}