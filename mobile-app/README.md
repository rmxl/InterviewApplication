# 1. LoginActivity.java

## Overview
This project contains an Android application that includes login functionality using an API. The `LoginActivity` class handles user authentication by sending login requests to a server.

## Integration Steps
To integrate this codebase into your project, follow these steps:


### 1. Add Dependencies
Ensure your `build.gradle` file includes the necessary dependencies for `OkHttp` and `JSON` parsing:
```gradle
implementation 'com.squareup.okhttp3:okhttp:4.9.3'
implementation 'org.json:json:20210307'
```

### 2. Update API Endpoint
Modify the API login endpoint in `Constants.java`:
```java
public class Constants {
    public static final String LOGIN_API = "https://your-api-endpoint.com/login";
}
```

### 4. Handle SSL Certificates (If Required)
The project includes `NukeSSLCertificates` to disable SSL certificate verification. Ensure it's included only if needed.

### 5. Register `LoginActivity` in `AndroidManifest.xml`
```xml
<activity android:name=".activities.LoginActivity">
    <intent-filter>
        <action android:name="android.intent.action.MAIN" />
        <category android:name="android.intent.category.LAUNCHER" />
    </intent-filter>
</activity>
```

## Class and Functionality Documentation

### `LoginActivity.java`
Handles user login by interacting with a remote server.

#### UI Components:
- `EditText editTextUsername` – Input field for username.
- `EditText editTextPassword` – Input field for password.
- `Button buttonLogin` – Triggers login process.

#### Key Functions:

- `initializeUI()` – Initializes UI components.
- `handleLoginClick()` – Validates input fields and initiates login request.
- `login(String username, String password)` – Sends login credentials via `OkHttp` and handles responses.
- `showToast(String message)` – Displays a toast message.
- `navigateToMainActivity()` – Navigates to the main screen upon successful login.
- `showProgress()` – Placeholder for showing a loading indicator.
- `hideProgress()` – Placeholder for hiding a loading indicator.

#### API Integration:
- Uses `OkHttp` for making network requests.
- Expects a JSON response containing user details.
- Stores logged-in user details using `Globals`.

## Usage Instructions
1. Launch the app.
2. Enter username and password.
3. Click the login button.
4. Upon successful authentication, the user is redirected to `MainActivity`.

## Error Handling
- Displays toast messages for empty input fields.
- Logs and shows error messages for failed login attempts.


# 2. MainActivity.java

## Overview

The `MainActivity.java` class is responsible for managing navigation in the application using a navigation drawer. It allows users to switch between different fragments dynamically.

## Integration Steps

To integrate `MainActivity.java` into your project, follow these steps:

### 1. Ensure Dependencies

Make sure your project includes the required dependencies in `build.gradle`:

```gradle
implementation 'androidx.appcompat:appcompat:1.4.0'
implementation 'androidx.drawerlayout:drawerlayout:1.1.1'
implementation 'com.google.android.material:material:1.6.1'
```

### 2. Add Activity to `AndroidManifest.xml`

Ensure that `MainActivity` is declared in your manifest:

```xml
<activity android:name=".activities.MainActivity" />
```

### 3. Define Required Layouts

The following layouts must be available in `res/layout/`:
- `activity_main.xml` (Contains the `DrawerLayout`, `Toolbar`, and `FrameLayout` for fragments)
- `nav_menu.xml` (Defines navigation menu items)

## Class and Functionality Documentation

### `MainActivity.java`

Handles navigation and fragment transactions within the application.

#### UI Components:
- `DrawerLayout drawerLayout` – The main navigation drawer container.
- `Toolbar toolbar` – The top toolbar for navigation.
- `NavigationView navigationView` – Handles navigation menu items.

#### Key Functions:

- `initializeUIComponents(Bundle savedInstanceState)`
  - Sets up the toolbar, navigation drawer, and initial fragment selection.

- `setupDrawer(Toolbar toolbar)`
  - Configures the drawer layout with a toggle button.

- `setupNavigationMenu(NavigationView navigationView)`
  - Handles navigation item selections and fragment transactions.

- `selectFragment(Fragment fragment, String tag)`
  - Dynamically loads and replaces fragments based on user selection.

- `closeDrawer()`
  - Ensures smooth drawer closing when a menu item is selected.

## Navigation Flow

1. When the app starts, `ScheduleFragment` is loaded by default.
2. Users can open the navigation drawer and select `ProfileFragment` or `ScheduleFragment`.
3. The drawer closes after selecting an item to ensure smooth UX.

## Error Handling

- Ensures drawer does not remain open unnecessarily.
- Uses fragment tags to manage back-stack navigation properly.

# 3. ScheduleActivity.java

## Overview

`ScheduleActivity.java` is responsible for scheduling and rescheduling appointments within the Android application. It provides a user interface for selecting dates, choosing available time slots, and sending scheduling requests to a backend server.

## UI Components

- `TextInputEditText textDate` - Displays and allows the user to select a date.
- `MaterialAutoCompleteTextView dropdownTime` - Dropdown menu for selecting a time slot.
- `MaterialButton buttonSchedule` - Triggers the scheduling process.
- `MaterialButton buttonDatePicker` - Opens the date picker dialog.
- `TextView textTitle` - Displays the title based on scheduling or rescheduling.

## Key Functions

### `onCreate(Bundle savedInstanceState)`
- Initializes UI components.
- Determines if the user is scheduling or rescheduling.
- Sets up the dropdown for available time slots.
- Adds event listeners for date selection and scheduling.

### `initializeUI()`
- Links UI components to their corresponding XML views.

### `updateTitle()`
- Updates the activity title based on whether the user is scheduling or rescheduling an appointment.

### `setupDropdowns()`
- Pre-fills the date field with today's date.
- Fetches available time slots for the selected date.

### `setDropdownAdapter(MaterialAutoCompleteTextView dropdown, List<String> items)`
- Binds a list of items to a dropdown menu.

### `showDatePicker()`
- Displays a date picker dialog to allow the user to choose a date.
- Updates the date field and fetches available slots.

### `fetchAvailableSlots(String date)`
- Sends a GET request to fetch available time slots from the backend.
- Parses the response and updates the dropdown menu.

### `updateTimeSlots(List<String> timeSlots)`
- Populates the dropdown with available time slots.
- Disables the dropdown and schedule button if no slots are available.

### `schedule()`
- Sends a scheduling or rescheduling request to the backend.
- Validates user input before sending the request.
- Disables the scheduling button to prevent duplicate submissions.

### `getTodayDate()`
- Returns the current date in `yyyy-MM-dd` format.

### `showToast(String message)`
- Displays a toast message to the user.

## API Integration

- **GET** `/api/available-slots?date=<selected_date>`
  - Retrieves available time slots for the given date.
- **POST** `/api/schedule`
  - Schedules an appointment with selected date and time.
- **POST** `/api/reschedule`
  - Reschedules an existing appointment with new date and time.

## Error Handling

- Displays toast messages for network failures or API errors.
- Disables scheduling button when no time slots are available.








Sure! Below are the detailed step-by-step instructions on how to set up and run your Android application from scratch, assuming you have the Android project files already but need guidance on setting it up properly for the first time.

---

### **Step 1: Install Android Studio**
If you haven’t installed Android Studio yet, here’s how to do it:

1. **Download Android Studio** from the official site: [https://developer.android.com/studio](https://developer.android.com/studio)
2. Follow the installation instructions for your OS (Windows, macOS, or Linux).
3. Once installed, open Android Studio.

---

### **Step 2: Set Up the Android Project**
1. **Create a New Project** in Android Studio if you haven’t already. If you're working with an existing project, you can just open it by going to **File > Open** and selecting the project directory.

2. If creating a new project, choose an appropriate template (like "Empty Activity").

---

### **Step 3: Modify `build.gradle` Files**
There are two `build.gradle` files in an Android project — one for the project-level and one for the app-level. You'll need to ensure both are properly set up with the required dependencies.

#### **1. Project-Level `build.gradle`**
Make sure the project-level `build.gradle` file includes the necessary repositories:

```gradle
buildscript {
    repositories {
        google()
        mavenCentral()
    }
    dependencies {
        classpath 'com.android.tools.build:gradle:7.0.4' // Or the latest version
        classpath "org.jetbrains.kotlin:kotlin-gradle-plugin:1.5.31" // If using Kotlin
    }
}

allprojects {
    repositories {
        google()
        mavenCentral()
    }
}
```

#### **2. App-Level `build.gradle`**
In the app-level `build.gradle` file, include the necessary dependencies for libraries like OkHttp, Gson, and Material Components.

Here’s a sample setup:

```gradle
plugins {
    id 'com.android.application'
    id 'kotlin-android' // If using Kotlin
}

android {
    compileSdk 30

    defaultConfig {
        applicationId "com.yourapp.name"
        minSdk 21
        targetSdk 30
        versionCode 1
        versionName "1.0"
    }

    buildTypes {
        release {
            minifyEnabled false
            proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
        }
    }
}

dependencies {
    implementation 'androidx.appcompat:appcompat:1.4.0'
    implementation 'com.google.android.material:material:1.6.1'
    implementation 'androidx.constraintlayout:constraintlayout:2.1.1'

    // OkHttp for making network requests
    implementation 'com.squareup.okhttp3:okhttp:4.9.3'

    // Gson for parsing JSON
    implementation 'com.google.code.gson:gson:2.8.8'

    // If using Kotlin
    implementation 'org.jetbrains.kotlin:kotlin-stdlib:1.5.31'

    // Any other dependencies your project needs
}
```

- **Important:** Ensure that the correct versions of libraries are used based on your project and Android Studio's compatibility.

---

### **Step 4: Set Up API Constants**
In your `Constants.java` file, add your API endpoint URLs. For example:

```java
public class Constants {
    public static final String LOGIN_API = "https://your-api-endpoint.com/login";
    public static final String BASE_URL = "https://your-api-endpoint.com"; // Base URL for API
}
```

Ensure that your URLs match your backend API routes.

---

### **Step 5: Set Up Permissions**
Make sure you add the necessary permissions in the `AndroidManifest.xml` file, especially if you are using features like the camera, internet, or writing to external storage. Here’s an example:

```xml
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.RECORD_AUDIO" />
<uses-permission android:name="android.permission.INTERNET" />
<uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />
```

---

### **Step 6: Create and Implement Activities**

- **LoginActivity.java:** 
  - This activity will handle user authentication.
  
- **MainActivity.java:** 
  - This is the main entry point where users can navigate to different fragments using a drawer layout.

- **ScheduleActivity.java:** 
  - Users can schedule or reschedule assessments here.

Ensure that you create the necessary layouts (`activity_main.xml`, `activity_login.xml`, etc.) in the `res/layout` directory for each activity.

---

### **Step 7: Create Necessary Fragments**
You’ll need to create fragments that will be loaded into your `MainActivity` using the navigation drawer:

1. **ScheduleFragment:** For displaying assessments.
2. **ProfileFragment:** For showing the user’s profile.

Example code for a simple fragment:

```java
public class ScheduleFragment extends Fragment {
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_schedule, container, false);
    }
}
```

Make sure to set up the fragments in your `MainActivity.java` for dynamic navigation.

---

### **Step 8: Handle SSL Certificates (If Needed)**
If you are facing SSL certificate issues, your project includes the `NukeSSLCertificates` class. This will bypass SSL verification for testing, but make sure to replace it with proper certificate validation before going to production.

To add `NukeSSLCertificates`, you'll need to follow these steps:

1. **Add the `NukeSSLCertificates.java` class** to your project.
2. **Use it in your network requests**:

```java
OkHttpClient client = NukeSSLCertificates.getUnsafeOkHttpClient();
```

Note: You must implement proper SSL verification in a production environment for security.

---

### **Step 9: Running the App**
1. **Build the Project:** In Android Studio, click on **Build > Make Project** or press `Ctrl + F9` (Windows/Linux) or `Cmd + F9` (macOS).
   
2. **Run the Application:** 
   - Connect a physical device or start an Android Emulator.
   - Click the **Run** button (green triangle) in Android Studio to deploy the app to your connected device or emulator.
   
3. **Debugging:** 
   - You can use the **Logcat** window to monitor any logs, errors, or issues that occur when the app is running.

---

### **Step 10: Test and Troubleshoot**
Test all features thoroughly:
- **Login functionality:** Ensure that the login process works by providing valid credentials.
- **Scheduling functionality:** Check if users can successfully select dates, times, and confirm appointments.
- **Navigation:** Ensure that the navigation drawer and fragments switch properly.
  
If you encounter errors, check **Logcat** for any error logs and fix them accordingly. Ensure that your API requests are set up correctly, and use `Toast` or `Log.e()` to debug any issues with the UI or network calls.

---

### **Step 11: Deploying Your App**
Once you have thoroughly tested your app, you can proceed with generating a signed APK or app bundle for release. Follow these steps:
1. **Generate Signed APK:** In Android Studio, go to **Build > Generate Signed Bundle / APK** and follow the wizard to generate the APK.

2. **Deploy to Google Play Store (if needed):** Follow the [Google Play Console instructions](https://developer.android.com/studio/publish) to upload your APK to the Play Store.

---

### **Conclusion**
After following these steps, you should have a fully functioning Android application set up with a proper build system, navigation, and network requests.