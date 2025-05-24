package org.anangappara.agoratest.api;

public class UrlResponse
{
    String channel;
    String token;
    public String url;
    public int id;

    public UrlResponse(String channel, String token, String url, int id)
    {
        this.channel = channel;
        this.token = token;
        this.url = url;
        this.id = id;
    }
}
