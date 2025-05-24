package org.anangappara.agoratest;

import java.security.SecureRandom;
import java.security.cert.X509Certificate;

import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLSocketFactory;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;

import okhttp3.OkHttpClient;

/**
 * This class provides a mechanism to bypass SSL certificate validation in Android applications.
 *
 * This is particularly useful in development or testing environments where self-signed certificates
 * are used and are not inherently trusted by the Android system. By disabling SSL validation, it allows
 * HTTPS requests to proceed even when certificates are invalid or untrusted.
 *
 * WARNING: This code should only be used in DEVELOPMENT or TEST environments.
 * Never use this code in PRODUCTION as it introduces serious security vulnerabilities.
 *
 * Purpose of this code:
 * 1. **Workaround for Self-Signed Certificates:**
 *    - Self-signed certificates that aren’t trusted by the Android CA store will cause HTTPS requests to fail.
 *    - This code suppresses SSL checks to let requests go through.
 *
 * 2. **Development and Testing:**
 *    - Useful for working with local servers or staging environments using non-standard certificates.
 *
 * 3. **SSL Validation Disabling:**
 *    - Bypasses both server and client certificate validation and disables hostname verification.
 *
 */

public class NukeSSLCertificates
{
    protected static final String TAG = "NukeSSLCerts";

    public static OkHttpClient getUnsafeOkHttpClient() {
        try {
            // Create a trust manager that does not validate certificate chains
            TrustManager[] trustAllCerts = new TrustManager[]{
                    new X509TrustManager() {
                        @Override
                        public void checkClientTrusted(X509Certificate[] chain, String authType) {}

                        @Override
                        public void checkServerTrusted(X509Certificate[] chain, String authType) {}

                        @Override
                        public X509Certificate[] getAcceptedIssuers() {
                            return new X509Certificate[]{};
                        }
                    }
            };

            // Install the all-trusting trust manager
            SSLContext sslContext = SSLContext.getInstance("SSL");
            sslContext.init(null, trustAllCerts, new SecureRandom());

            // Create an ssl socket factory with our all-trusting manager
            SSLSocketFactory sslSocketFactory = sslContext.getSocketFactory();

            return new OkHttpClient.Builder()
                    .sslSocketFactory(sslSocketFactory, (X509TrustManager) trustAllCerts[0])
                    .hostnameVerifier((hostname, session) -> true) // Disable hostname verification
                    .build();

        } catch (Exception e) {
            throw new RuntimeException("Failed to create a trust-all OkHttpClient", e);
        }
    }
}