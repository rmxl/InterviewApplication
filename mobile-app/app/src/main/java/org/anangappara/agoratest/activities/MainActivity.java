// MainActivity.java
package org.anangappara.agoratest.activities;

import android.os.Bundle;

import androidx.appcompat.app.ActionBarDrawerToggle;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.fragment.app.Fragment;

import com.google.android.material.navigation.NavigationView;

import org.anangappara.agoratest.R;
import org.anangappara.agoratest.fragments.ProfileFragment;
import org.anangappara.agoratest.fragments.ScheduleFragment;

public class MainActivity extends AppCompatActivity
{

    private DrawerLayout drawerLayout;

    // Constants for fragment tags
    private static final String FRAGMENT_SCHEDULE = "ScheduleFragment";
    private static final String FRAGMENT_PROFILE = "ProfileFragment";

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        initializeUIComponents(savedInstanceState);
    }

    /**
     * Initializes the UI components and sets up the listeners.
     */
    private void initializeUIComponents(Bundle savedInstanceState)
    {
        // Set up toolbar
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        // Initialize DrawerLayout and NavigationView
        drawerLayout = findViewById(R.id.drawer_layout);
        NavigationView navigationView = findViewById(R.id.navigation_view);

        setupDrawer(toolbar);
        setupNavigationMenu(navigationView);

        // Load default fragment if there is no saved instance state
        if (savedInstanceState == null)
        {
            selectFragment(new ScheduleFragment(), FRAGMENT_SCHEDULE);
            navigationView.setCheckedItem(R.id.nav_assessment);
        }
    }

    /**
     * Configures the DrawerLayout with a toggle button.
     */
    private void setupDrawer(Toolbar toolbar)
    {
        ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(
                this, drawerLayout, toolbar,
                R.string.navigation_drawer_open, R.string.navigation_drawer_close);

        drawerLayout.addDrawerListener(toggle);
        toggle.syncState();
    }

    /**
     * Sets up the NavigationView menu item selection listener.
     */
    private void setupNavigationMenu(NavigationView navigationView)
    {
        navigationView.setNavigationItemSelectedListener(item ->
        {
            Fragment selectedFragment = null;
            String tag = null;

            if (item.getItemId() == R.id.nav_assessment)
            {
                selectedFragment = new ScheduleFragment();
                tag = FRAGMENT_SCHEDULE;
            }
            else if (item.getItemId() == R.id.nav_profile)
            {
                selectedFragment = new ProfileFragment();
                tag = FRAGMENT_PROFILE;
            }

            if (selectedFragment != null)
            {
                selectFragment(selectedFragment, tag);
            }

            closeDrawer();
            return true;
        });
    }

    /**
     * Replaces the current fragment with the selected one.
     *
     * @param fragment The fragment to display.
     * @param tag      A tag to identify the fragment in the back stack.
     */
    private void selectFragment(Fragment fragment, String tag)
    {
        getSupportFragmentManager().beginTransaction()
                .replace(R.id.frame_layout, fragment, tag)
                .commit();
    }

    /**
     * Closes the DrawerLayout gracefully.
     */
    private void closeDrawer()
    {
        if (drawerLayout.isDrawerVisible(GravityCompat.START))
        {
            // delay for smooth drawer sliding animation
            drawerLayout.postDelayed(() -> drawerLayout.closeDrawer(GravityCompat.START), 150);
        }
    }
}