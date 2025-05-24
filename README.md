
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

## Details of Admin Panel

## Endpoints

### Time Slot Management

#### Retrieve Time Slots
- **GET** `/time-slots`
  - Retrieves all available time slots in the system.

- **GET** `/time-slots/date/{date}`
  - Fetches time slots available for a specific date.

- **GET** `/time-slots/{id}`
  - Gets detailed information about a specific time slot using its ID.

- **GET** `/available-slots`
  - Returns only the time slots that are currently available for booking.

#### Rescheduling
- **POST** `/reschedule`
  - Allows rescheduling an existing assessment to a different time slot.

### Assessment Management

#### Scheduling and Retrieval
- **POST** `/schedule`
  - Creates a new assessment by storing the request in the database.

- **GET** `/get-assessment/{username}`
  - Retrieves assessment details for a specific user.

- **GET** `/assessment-requests/{assessmentRequestId}`
  - Gets details of a specific assessment request.

- **GET** `/assessments/month`
  - Retrieves all assessments for the current month.

- **GET** `/assessments/date/{date}`
  - Gets all assessments scheduled for a specific date.

- **GET** `/assessments/pending`
  - Returns assessments that are pending and need attention.

#### Updates and Ratings
- **POST** `/save-rating`
  - Saves user ratings for completed assessments.

- **POST** `/changestatus`
  - Updates the status of an assessment (e.g., from pending to completed).

### Agora Video Integration

- **POST** `/generate-token`
  - Generates authentication tokens for Agora video calls.

- **GET** `/get-url/{username}`
  - Retrieves the video call URL for a specific user.

- **POST** `/create-url`
  - Creates a new video call URL for an assessment.

- **GET** `/get-result/{username}`
  - Fetches assessment results for a specific user.

### Authentication

- **POST** `/app-login`
  - Handles user authentication for the mobile application.

- **GET** `/user-info/{id}`
  - Retrieves user profile information by user ID.



## Database Structure

### Migrations

#### `create_jobs_table.php`
This migration creates the `jobs` table which stores different job types in the system. Each job has a unique job type identifier. This serves as a reference table for various job roles throughout the application.

#### `create_backend_guys_table.php`
This migration establishes the `backend_guys` table for administrative users. It includes fields for unique usernames and passwords (stored as hashes) to manage system authentication for backend operators.

#### `create_users_table.php`
The `users` table stores regular user accounts with personal information and job preferences. Each user is associated with a specific job type through a foreign key relationship. The table includes experience level information defaulting to "newbie" for new users.

#### `create_questions_table.php`
This migration creates the `questions` table for storing assessment questions. Each question is categorized by job type and experience level, with fields for the question text and a detailed body/description.

#### `create_time_slots.php`
The `time_slots` table manages availability schedules for backend administrators. It records dates and times when administrators are available for assessments, with a boolean flag to track slot availability.

#### `create_assessment_requests_table.php`
This migration sets up the `assessment_requests` table to track user assessment sessions. It links users with backend administrators and time slots, records assessment types, and tracks completion status and ratings.

#### `create_sessions_table.php`
The `sessions` table handles user session management for the application, storing login information, IP addresses, and other session-related data for authenticated users.

#### `create_url_table.php`
This migration creates the `url_table` for storing URLs associated with assessment requests. It includes fields for the URL itself along with channel and token information for secure access.

### Seeders

#### `BackendGuySeeder.php`
This seeder populates the `backend_guys` table with default administrative accounts including "admin" and "moderator" users with secure password hashes.

#### `DatabaseSeeder.php`
The main seeder file that orchestrates the execution of all other seeders in the correct order to maintain database integrity and foreign key relationships.

#### `JobSeeder.php`
This seeder populates the `jobs` table with various job types such as "tiffin_master", "tea_master", "coffee_master", and others, establishing the foundation for job-based functionality.

#### `QuestionSeeder.php`
A comprehensive seeder that creates assessment questions for each job category. It adds specific, role-related questions with detailed answer bodies that cover responsibilities, skills, and scenarios for each job type.

#### `UserSeeder.php`
This seeder creates a default user account "John Doe" with appropriate credentials and associates it with the "tiffin_master" job type for testing and initial setup purposes.

### Database Relationships

- Users are associated with specific job types
- Questions are categorized by job type and experience level
- Assessment requests link users with backend administrators and time slots
- Time slots are connected to specific backend administrators
- URLs are associated with specific assessment requests



## Models Used

### `AssessmentRequest.php`
The `AssessmentRequest` model represents assessment sessions requested by users:
- **Fillable fields**: `user_id`, `backend_guy_id`, `time_slot_id`, `assessment_type`
- **Relationships**:
  - Belongs to a `User`
  - Belongs to a `BackendGuy` (administrator)
  - Belongs to a `TimeSlot`

This model tracks assessment requests, connecting users with admins during specific time slots.

### `BackendGuy.php`
The `BackendGuy` model extends Laravel's `Authenticatable` class and represents system administrators:
- **Fillable fields**: `username`, `password`
- **Hidden fields**: `password` (not returned in API responses)
- **Relationships**:
  - Has many `TimeSlot` records

This model manages authentication and availability scheduling for administrative users.

### `Job.php`
The `Job` model stores different job types available in the system:
- **Fillable fields**: `jobType`
- **Relationships**:
  - Has many `User` records (via `jobType_Id`)
  - Has many `Question` records (via `jobType_Id`)

This model serves as a reference table for job categories and connects users to their job types.

### `Question.php`
The `Question` model represents assessment questions:
- **Fillable fields**: `text`, `body`, `job_type`, `experience_level`
- Uses Laravel's `HasFactory` trait

This model stores the questions used during user assessments, categorized by job type and experience level.

### `TimeSlot.php`
The `TimeSlot` model represents available time slots for assessments:
- **Fillable fields**: `backend_guy_id`, `date`, `start_time`, `end_time`, `is_available`
- **Relationships**:
  - Belongs to a `BackendGuy`

This model manages the scheduling system for assessment sessions.

### `UrlTable.php`
The `UrlTable` model manages URLs associated with assessment requests:
- **Table name**: `url_table`
- **Fillable fields**: `assessment_request_id`, `url`, `channel`, `token`
- **Relationships**:
  - Belongs to an `AssessmentRequest`

This model stores URLs for assessment sessions, including security tokens and channel information.

### `User.php`
The `User` model extends Laravel's `Authenticatable` class for regular system users:
- **Fillable fields**: `name`, `username`, `password`
- **Relationships**:
  - Belongs to a `Job` (via `jobType_Id`)

This model handles user authentication and stores basic user information.

## Model Relationships

The application implements the following key relationships:

- Users are linked to job types
- Questions are categorized by job type
- Assessment requests connect users, administrators, and time slots
- Time slots are associated with specific administrators
- URLs are linked to specific assessment requests

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

## Details

# 1. AssessmentResultActivity 

## Overview
The `AssessmentResultActivity` is an Android activity responsible for displaying the results of an assessment fetched from an API. It makes an HTTP request to retrieve the assessment data, processes the response, and updates the UI accordingly.

## Class: `AssessmentResultActivity`

### Purpose
This class extends `AppCompatActivity` and is responsible for:
1. Fetching the assessment result from an API.
2. Parsing the JSON response.
3. Updating the UI based on the assessment outcome.

### Dependencies
- **OkHttp**: Used for making HTTP requests.
- **Gson**: Used for parsing JSON responses.
- **Android UI Components**: `LinearLayout`, `TextView` for displaying data.

## UI Components
- `LinearLayout containerPass`: Displays when the user has passed the assessment.
- `LinearLayout containerFail`: Displays when the user has failed the assessment.
- `TextView textAssessmentName`: Displays the name of the assessment.
- `TextView textDate`: Displays the date of the assessment.
- `TextView textTime`: Displays the time of the assessment.
- `TextView textExperience`: Displays the experience required.

## Key Methods

### `onCreate(Bundle savedInstanceState)`
- Initializes the activity and sets up the UI elements.
- Calls `fetchAssessmentResult()` to retrieve data.

### `fetchAssessmentResult()`
- Constructs the API URL using `Constants.HOST`.
- Uses `OkHttpClient` (configured with `NukeSSLCertificates.getUnsafeOkHttpClient()`) to make an HTTP GET request.
- Logs errors if the request fails.
- Parses the JSON response using `Gson` if the request is successful.
- Calls `loadAssessmentData(result)` to update the UI.

### `loadAssessmentData(AssessmentResultResponse response)`
- Updates UI elements based on the assessment result.
- If the result is "pass", it shows `containerPass` and hides `containerFail`.
- If the result is "fail", it shows `containerFail` and hides `containerPass`.
- Updates `textAssessmentName`, `textDate`, `textTime`, and `textExperience` with values from the API response.

## API Integration
- **Endpoint**: `Constants.HOST + "/api/get-result/$username"`
- **Request Type**: `GET`
- **Response Handling**:
  - Parses JSON response into `AssessmentResultResponse` object.
  - Runs `loadAssessmentData()` on the UI thread to update UI elements.

## Error Handling
- Logs errors when API requests fail (`onFailure` in `Callback`).
- Logs error codes for unsuccessful responses.

## Security Considerations
- Uses `NukeSSLCertificates.getUnsafeOkHttpClient()`, which disables SSL verification (should be replaced with a proper certificate validation method for production).

## Additional Notes

- Consider adding a loading indicator while fetching data.
- UI updates should handle potential `null` values from the API response to prevent crashes.

---


# 2. AssessmentScheduleActivity

## Overview
`AssessmentScheduleActivity.java` is an Android activity responsible for managing and scheduling assessments or meetings within the application. This activity enables users to schedule, view, and join assessments while ensuring seamless user interaction and data handling.

## Features
- Displays a list of scheduled assessments.
- Allows users to schedule new assessments.
- Provides functionality to join an ongoing assessment.
- Integrates with backend services to fetch and update assessment details.
- Implements UI elements for intuitive navigation and scheduling.


## Usage
1. Open the application and navigate to the assessment scheduling section.
2. View existing scheduled assessments.
3. Tap on an assessment to view details or join.
4. Use the scheduling feature to add new assessments.

## Code Structure
- **onCreate()**: Initializes UI components and loads scheduled assessments.
- **scheduleAssessment()**: Handles user input for scheduling a new assessment.
- **joinAssessment()**: Handles the logic for users to join an assessment.
- **fetchAssessments()**: Retrieves assessment data from the backend or database.
- **updateUI()**: Updates the user interface with the latest assessment details.

## Potential Enhancements
- Implement push notifications for upcoming assessments.
- Add real-time updates using WebSockets or Firebase Realtime Database.
- Include user authentication and role-based access control.



# 3. LoginActivity

## Overview
The `LoginActivity` class in the `org.anangappara.agoratest.activities` package handles user authentication in the Android application. It provides a login interface where users enter their credentials, which are then validated against the backend API. Upon successful authentication, the user is redirected to the `MainActivity`.

## Features
- **User Authentication:** Sends a login request with the username and password to the backend.
- **Error Handling:** Displays appropriate messages for invalid credentials or server errors.
- **Session Management:** Stores the logged-in user's ID and username globally using `Globals`.
- **Redirection:** Navigates to `MainActivity` upon successful login.
- **SSL Handling:** Uses `NukeSSLCertificates` to bypass SSL certificate issues.

## Components
### UI Elements
- `EditText editTextUsername`: Input field for the username.
- `EditText editTextPassword`: Input field for the password.
- `Button buttonLogin`: Triggers login authentication.

### Dependencies
- **Volley Library:** Handles HTTP requests.
- **NukeSSLCertificates:** Disables SSL certificate validation.
- **Globals:** Stores user session data globally.

## Login Process
1. **User Input:** User enters a username and password.
2. **Validation:** Checks if both fields are non-empty.
3. **API Request:** Sends a POST request to `/api/app-login` with login credentials.
4. **Response Handling:**
   - On success: Stores user data and redirects to `MainActivity`.
   - On failure: Displays an error message.

## API Request Details
- **Endpoint:** `Constants.HOST + "/api/app-login"`
- **Method:** `POST`
- **Request Body:** JSON containing `username` and `password`
- **Success Response:** JSON with a message and user details
- **Error Handling:** Displays appropriate error messages based on response status

## Navigation Flow
- **Successful Login:** `LoginActivity` → `MainActivity`
- **Failed Login:** Displays error and stays on `LoginActivity`

## Notes
- The app uses an intent filter to allow launching from an external browser.
- SSL-related code is commented out, but `NukeSSLCertificates.nuke();` is active.
- Additional debugging logs are present for tracking intent URIs.

## Future Improvements
- Implement proper SSL certificate validation.
- Enhance error handling for different server response scenarios.
- Securely store session tokens instead of using global variables.


# 4. MainActivity

## Overview
`MainActivity.java` is the main entry point of the application after user login. It serves as the primary UI framework, integrating a navigation drawer for accessing different sections of the app, such as scheduling assessments and managing user profiles.

## Features
- Implements a **navigation drawer** for easy access to different sections.
- Uses **fragments** to dynamically load content without reloading the activity.
- **Default fragment** is set to `ScheduleFragment` upon activity launch.
- Integrates **SSL certificate handling** with `NukeSSLCertificates.nuke()`.
- Includes a **toolbar with a toggle button** to open and close the navigation drawer.

## Components
### UI Elements
- **Toolbar** (`R.id.toolbar`) - Displays the app title and navigation toggle.
- **DrawerLayout** (`R.id.drawer_layout`) - Manages the navigation drawer.
- **NavigationView** (`R.id.navigation_view`) - Contains menu items for navigation.
- **FrameLayout** (`R.id.frame_layout`) - Placeholder for fragments.

### Fragments Used
- **ScheduleFragment** (default) - Displays scheduled assessments.
- **ProfileFragment** - Displays user profile information.

## Navigation Handling
The navigation drawer allows users to switch between different sections:
1. `ScheduleFragment` (Assessments Section)
2. `ProfileFragment` (User Profile Section)

Upon selecting a menu item, the corresponding fragment is loaded dynamically.

## Default Fragment Loading
When the activity starts, `ScheduleFragment` is loaded by default:
```java
if (savedInstanceState == null) {
    getSupportFragmentManager().beginTransaction()
        .replace(R.id.frame_layout, new ScheduleFragment()).commit();
    navigationView.setCheckedItem(R.id.nav_assessment);
}
```

## SSL Certificate Handling
To handle SSL-related issues, the app calls:
```java
NukeSSLCertificates.nuke();
```
This ensures the app can communicate securely with backend servers.

## Dependencies
This activity relies on the following Android components:
- `androidx.appcompat.app.AppCompatActivity`
- `androidx.drawerlayout.widget.DrawerLayout`
- `com.google.android.material.navigation.NavigationView`
- `androidx.fragment.app.Fragment`

## Notes
- The **`HomeFragment`** is currently commented out and can be enabled if needed.
- `postDelayed` is used to ensure smooth closing of the navigation drawer after a menu item is selected.

## Future Enhancements

- Implement user session management to persist login state.

---

# 5. ScheduleActivity

## Overview
`ScheduleActivity.java` is an Android activity that allows users to schedule or reschedule an assessment by selecting a date and time slot. The activity fetches available slots from an API and allows users to confirm their selection.

## Features
- **Date Selection**: Users can pick a date using `MaterialDatePicker`, restricted to the next 7 days.
- **Fetching Available Slots**: Retrieves available time slots for the selected date via an API call.
- **Dropdown for Time Selection**: Displays fetched time slots in a dropdown (`MaterialAutoCompleteTextView`).
- **Scheduling API Integration**: Sends a scheduling request to the backend API.
- **Rescheduling Support**: Handles rescheduling logic when invoked in reschedule mode.

## UI Components
- `TextInputEditText textDate`: Displays the selected date.
- `MaterialAutoCompleteTextView dropdownTime`: Displays available time slots.
- `MaterialButton buttonSchedule`: Submits the scheduling request.
- `MaterialButton buttonDatePicker`: Opens the date picker dialog.
- `TextView textTitle`: Changes title based on scheduling or rescheduling mode.

## API Calls
- **Fetching Available Slots**:
  - URL: `Constants.HOST + "/api/available-slots?date=" + selectedDate`
  - HTTP Method: `GET`
  - Response: List of available time slots
- **Scheduling Request**:
  - URL: `Constants.HOST + "/api/schedule"`
  - HTTP Method: `POST`
  - Payload: JSON containing user details, date, and time
- **Rescheduling Request**:
  - URL: `Constants.HOST + "/api/reschedule"`
  - HTTP Method: `POST`
  - Payload: JSON similar to scheduling request but for rescheduling

## Key Methods
- `setupDropdowns()`: Initializes dropdown menus for time slots.
- `showDatePicker()`: Displays the date picker dialog and sets constraints.
- `fetchAvailableSlots(String date)`: Fetches available time slots from the backend.
- `updateTimeSlots(List<String> timeSlots)`: Updates the dropdown with fetched time slots.
- `schedule()`: Sends a scheduling or rescheduling request to the backend.
- `getTodayDate()`: Returns the current date in `yyyy-MM-dd` format.

## Error Handling
- Displays a toast message if the API request fails.
- Disables scheduling if no slots are available.
- Logs errors for debugging (`Log.e`).

## Future Enhancements
- Add user authentication to fetch user-specific slots.
- Implement a confirmation screen after successful scheduling.
- Improve error handling with detailed API response messages.

## Dependencies
- **Material Components**: Used for UI elements like `MaterialDatePicker` and `MaterialAutoCompleteTextView`.
- **OkHttp & Gson**: Used for making API calls and parsing JSON responses.

---

# 6. VideoActivity 

## Overview
`VideoActivity.java` is an Android activity that integrates the Agora Video SDK to enable real-time video calling. It manages permissions, initializes the Agora SDK, sets up local and remote video streams, and handles user events such as joining and leaving a channel.

## Features
- Requests necessary permissions (camera, microphone, etc.).
- Initializes and configures Agora's RTC engine.
- Enables video streaming and starts a video call.
- Sets up local and remote video views dynamically.
- Displays real-time event messages (e.g., user joins, leaves).
- Cleans up Agora resources on activity destruction.

## Key Methods & Functionality

### `initializeAgoraVideoSDK()`
- Initializes the Agora RTC engine.
- Configures event handlers for real-time updates.

### `enableVideo()`
- Enables video mode and starts a preview.

### `setupLocalVideo()`
- Creates and attaches a SurfaceView for local video.

### `joinChannel()`
- Connects to the Agora channel and starts publishing audio/video.

### `setupRemoteVideo(int uid)`
- Displays remote video for the given user ID when they join.

### `cleanupAgoraEngine()`
- Stops preview and leaves the Agora channel on activity destruction.


## Additional Enhancements
- Implement UI buttons for muting, switching cameras, and leaving the call.
- Use Agora's token server for dynamic token generation.
- Handle network changes and reconnections efficiently.

---


