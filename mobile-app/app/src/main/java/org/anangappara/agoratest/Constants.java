package org.anangappara.agoratest;

public class Constants
{
    //    public static String HOST = System.getenv("APP_URL") != null ? System.getenv("APP_URL") : "http://localhost:8000";
//    public static String HOST = "https://172.20.10.3";
    public static String HOST = "http://192.168.150.106:8000";

    public static String API_LOGIN = HOST + "/api/app-login";
    public static String API_CANCEL_INSTANT_ASSESSMENT = HOST + "/api/cancel-assessment";
    public static String API_SCHEDULE_ASSESSMENT = HOST + "/api/schedule";
    public static String API_GET_ASSESSMENT_URL = HOST + "/api/get-url/";
    public static String API_GET_ASSESSMENT = HOST + "/api/get-assessment/";
    public static String API_USER_INFO = HOST + "/api/user-info/";

    public static int latestAssessmentId = -1;
}
