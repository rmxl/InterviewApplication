
# Frenvi Admin Panel

## Setup

You need to host this application on a server before attempting to run it.

After hosting, execute the following commands:

```bash
composer install
cp .env.example .env
```

Add the following key values to the new .env file:

- 'AGORA_APP_ID' : Your agora app id
- 'HOST': At what url your dashboard is hosted
- 'APP_URL': Set as same as 'HOST'
- 'DB_CONNECTION' and other 'DB_' variables : Specify the details of your database connection


Then,

```bash
php artisan migrate
php artisan db:seed
php artisan key:generate
php artisan config:cache
php artisan config:clear
php artisan serve
```

# Mobile Application

## Setup

### Prerequisites
Before running this activity, ensure you have:
- An Agora developer account ([Sign up here](https://www.agora.io/))
- An App ID from Agora Console
- A valid Agora token (for authentication)
- Required permissions granted by the user

### Setup & Usage

### 1. Add Dependencies
Ensure your `build.gradle` includes the required Agora dependency:
```gradle
implementation 'io.agora.rtc:full-sdk:4.0.0'
```

### 2. Configure App ID and Token
Replace the following placeholders in `VideoActivity.java` with your actual Agora credentials:
```java
private String myAppId = "YOUR_AGORA_APP_ID";
private String channelName = "YOUR_CHANNEL_NAME";
private String token = "YOUR_AGORA_TOKEN";
```

### 3. Handle Permissions
The app requests camera and microphone permissions automatically. Ensure they are declared in `AndroidManifest.xml`:
```xml
<uses-permission android:name="android.permission.CAMERA"/>
<uses-permission android:name="android.permission.RECORD_AUDIO"/>
```

### 4. Running the App
- Launch the activity.
- Accept the requested permissions.
- The app will start a video call and display your local video.
- When another user joins, their video stream appears in the remote container.
d to update UI elements.

