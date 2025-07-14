# Interview Application System

A comprehensive interview management platform consisting of an admin panel (Laravel) and a mobile application (Android) for conducting video-based technical assessments.

## Project Overview

This system enables organizations to conduct structured technical interviews with real-time video calls, automated question banks, assessment tracking, and rating systems. The platform supports both scheduled and instant assessments across multiple job categories.

# Admin Panel

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

## Admin Panel Features

### **Core Functionality**
- **Dashboard Management**: Comprehensive overview of scheduled and instant assessments
- **Time Slot Management**: Create, edit, and manage interviewer availability
- **Assessment Scheduling**: Schedule interviews with automatic time slot allocation
- **Real-time Video Calls**: Integrated Agora video calling system
- **Assessment Tracking**: Monitor pending, ongoing, and completed assessments

### **User Management**
- **Backend Administrator Authentication**: Secure login system for interviewers
- **User Profile Management**: View candidate information and experience levels
- **Job Category Management**: Support for multiple job types (tiffin_master, tea_master, coffee_master, etc.)

### **Assessment Features**
- **Dynamic Question Bank**: Job-specific and experience-level-based questions
- **Rating System**: Score candidates on a 0-10 scale
- **Assessment Types**: Support for both scheduled and instant interviews
- **Interview Recording**: Audio/video recording capabilities during assessments
- **Result Management**: Track assessment outcomes and candidate performance

### **Scheduling System**
- **Calendar Integration**: Visual calendar with assessment highlights
- **Time Slot Generation**: Automated slot creation for 7-day periods
- **Availability Management**: Real-time slot availability tracking
- **Rescheduling Support**: Easy interview rescheduling functionality

### **Video Call Integration**
- **Agora SDK Integration**: Professional video calling experience
- **Secure Token Generation**: Dynamic access tokens for video sessions
- **Multi-user Support**: Handle multiple concurrent interviews
- **Recording Capabilities**: Session recording for review purposes

---

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

## Mobile Application Features

### **User Authentication**
- **Secure Login**: Username/password authentication with backend validation
- **Session Management**: Persistent login sessions
- **User Profile**: Display user information and job preferences

### **Assessment Management**
- **Schedule Interviews**: Book available time slots up to 7 days in advance
- **Assessment History**: View past interview results and ratings
- **Instant Assessments**: Request immediate interviews when available
- **Rescheduling**: Modify existing interview appointments

### **Video Call Features**
- **High-Quality Video**: Agora SDK-powered video calling
- **Real-time Communication**: Low-latency audio/video streaming
- **Permission Management**: Automatic camera and microphone access
- **Cross-platform Support**: Optimized for Android devices

### **User Experience**
- **Material Design**: Modern, intuitive interface
- **Date Picker**: Easy-to-use calendar for scheduling
- **Notification System**: Assessment reminders and updates
- **Offline Support**: Basic functionality without internet connection

### **Assessment Tracking**
- **Result Display**: View pass/fail status with detailed feedback
- **Assessment Details**: Date, time, and experience level information
- **Performance History**: Track improvement over multiple assessments
