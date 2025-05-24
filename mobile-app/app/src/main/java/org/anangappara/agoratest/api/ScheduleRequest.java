package org.anangappara.agoratest.api;

public class ScheduleRequest
{
    String username;
    String date;
    String start_time;
    String assessment_type;

    public ScheduleRequest(String username, String date, String start_time, String assessment_type)
    {
        this.username = username;
        this.date = date;
        this.start_time = start_time;
        this.assessment_type = assessment_type;
    }
}
