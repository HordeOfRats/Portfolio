package com.example.gnaw;

import static android.content.ContentValues.TAG;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.room.Room;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;
import java.util.*;

import com.google.android.gms.tasks.*;
import com.google.firebase.firestore.*;

//class for entering the app as an already logged in user
public class selectUser extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_select_user);
        populateUsers();
        backButton();
    }

    //function to get all logged in users stored in the local room database on the device
    public void populateUsers(){
        LUDatabase db = Room.databaseBuilder(getApplicationContext(),
                LUDatabase.class, "logged-users-2").allowMainThreadQueries().build();
        UserDao userDao = db.userDao();
        List<User> users = userDao.getAll();
        //add a button for each user
        for (int i=0; i< users.size(); i++){
            addUserButton(users.get(i).id, users.get(i).username);
        }

    }

    //add a clickable button to log in as a specific user
    public void addUserButton(String ID, String username){
        ScrollView loggedScroll = (ScrollView) findViewById(R.id.loggedInScrollView);
        LinearLayout loggedLinear = (LinearLayout) findViewById(R.id.loggedInLinear);


        //define our new button
        Button newUserButton = new Button(this);
        //add attributes, setting the text to the username the function received
        newUserButton.setLayoutParams(new LinearLayout.LayoutParams(LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.WRAP_CONTENT));
        newUserButton.setText(username);
        newUserButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //when the new button is clicked, go to the home screen, putting the correct id and username as extra in intent
                Intent i = new Intent(getApplicationContext(), HomeScreen.class);
                i.putExtra("LOGGED_USER", username);
                i.putExtra("LOGGED_ID", ID);
                startActivity(i);
                //quit the select user activity
                finish();
            }
        });
        //add the new button to the linear layout
        loggedLinear.addView(newUserButton);
    }

    //function for the back button
    public void backButton(){
        Button backButton = (Button) findViewById(R.id.SUBackButton);
        backButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
    }
}