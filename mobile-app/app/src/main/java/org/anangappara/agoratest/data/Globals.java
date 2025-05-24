package org.anangappara.agoratest.data;

import android.app.Application;

public class Globals extends Application
{
    private String username;
    private int id;

    private static Globals instance;

    @Override
    public void onCreate()
    {
        super.onCreate();
        instance = this;
    }

    public static Globals getInstance()
    {
        return instance;
    }

    public String getUserName()
    {
        return username;
    }

    public void setUserName(String userName)
    {
        this.username = userName;
    }

    public int getUserId()
    {
        return id;
    }

    public void setUserId(int id)
    {
        this.id = id;
    }
}

