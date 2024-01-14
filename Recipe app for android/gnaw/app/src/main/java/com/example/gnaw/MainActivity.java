package com.example.gnaw;

import static android.content.ContentValues.TAG;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.room.BuiltInTypeConverters;
import androidx.room.Room;

import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;
import java.util.*;

import com.google.android.gms.tasks.*;
import com.google.firebase.firestore.*;


//initial class, used to login, create accounts, and view user guide
public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        //activate buttons
        checkLogin();
        goCreateAccount();
        goLoggerUsers();
        goGuide();
        //start the background service which uses the users phone to do database calculations and update the database
        Intent intent = new Intent(this, updateRatingService.class);
        startService(intent);

        //generateUser();
    }

    //ensures that the database updating service is stopped when the app is closed (we're not trying to be too malware'y after all)
    protected void onDestroy() {
        super.onDestroy();
        Intent intent = new Intent(this, updateRatingService.class);
        stopService(intent);
    }


    //test function to try generating data to the firestore
    //this is completely unused in the final app
    private void generateUser(){
        FirebaseFirestore db = FirebaseFirestore.getInstance();

        Button loginButton  = (Button) findViewById(R.id.confirmLogin);

        loginButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Map<String, Object> user = new HashMap<>();
                user.put("username", "Ada");
                user.put("password", "Lovelace");

                db.collection("users")
                        .add(user)
                        .addOnSuccessListener(new OnSuccessListener<DocumentReference>() {
                            @Override
                            public void onSuccess(DocumentReference documentReference) {
                                Log.d(TAG, "DocumentSnapshot added with ID: " + documentReference.getId());
                            }
                        })
                        .addOnFailureListener(new OnFailureListener() {
                            @Override
                            public void onFailure(@NonNull Exception e) {
                                Log.w(TAG, "Error adding document", e);
                            }
                        });

            }
        });

    }

    //function to check the entered details when the login (enter) button is pressed
    private void checkLogin(){
        FirebaseFirestore db = FirebaseFirestore.getInstance();
        //get the entered details from edit texts
        TextView userInput = (TextView) findViewById(R.id.usernameInput);
        TextView passInput = (TextView) findViewById(R.id.passwordInput);

        Button loginButton  = (Button) findViewById(R.id.confirmLogin);

        loginButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //check if the user has an internet connection
                if (isOnline() == true){
                    //check for a database entry in users which matches the entered data
                    db.collection("users")
                            .whereEqualTo("password", (passInput.getText().toString())).whereEqualTo("username", (userInput.getText().toString()))
                            .get()
                            .addOnCompleteListener(new OnCompleteListener<QuerySnapshot>() {
                                @Override
                                public void onComplete(@NonNull Task<QuerySnapshot> task) {
                                    if (task.isSuccessful()) {
                                        if (task.getResult().isEmpty()) {
                                            //if no such account exists, toast "invalid login"
                                            Log.d(TAG, "No document found");
                                            Toast toast = Toast.makeText(getApplicationContext(),
                                                    "Invalid login",
                                                    Toast.LENGTH_SHORT);
                                            toast.show();
                                        } else {
                                            //if the account exists, get the document id for it, and the username
                                            String loggedUser = null;
                                            String loggedID = null;
                                            for (QueryDocumentSnapshot document : task.getResult()) {
                                                Log.d(TAG, "DocumentSnapshot data: " + document.getData());
                                                loggedUser = document.getString("username");
                                                loggedID = document.getId();
                                            }

                                            //add the user to the "logged-users-2" local room database if they're not already there
                                            LUDatabase db = Room.databaseBuilder(getApplicationContext(),
                                                    LUDatabase.class, "logged-users-2").allowMainThreadQueries().build();
                                            UserDao userDao = db.userDao();
                                            List<User> users = userDao.getAll();
                                            User userToAdd = new User();
                                            userToAdd.username = loggedUser;
                                            userToAdd.id = loggedID;
                                            userDao.insertAll(userToAdd);
                                            users = userDao.getAll();
                                            for (int i = 0; i < users.size(); i++) {
                                                Log.d(TAG, "Logged users: " + users.get(i).username + users.get(i).id);

                                            }

                                            //start the home screen activity, putting the user id and username as extras
                                            Intent i = new Intent(getApplicationContext(), HomeScreen.class);
                                            i.putExtra("LOGGED_USER", loggedUser);
                                            i.putExtra("LOGGED_ID", loggedID);
                                            startActivity(i);
                                        }


                                    } else {
                                        Log.d(TAG, "Error getting documents: ", task.getException());
                                    }
                                }
                            });
                }
                else{
                    //inform the user to connect to the internet
                    Toast toast = Toast.makeText(getApplicationContext(),
                            "Please connect to the internet",
                            Toast.LENGTH_SHORT);
                    toast.show();
                }
            }
        });
    }


    //function to check if the user is connected to the internet, returns true if so
    public boolean isOnline() {

        ConnectivityManager cm = (ConnectivityManager) this.getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo netInfo = cm.getActiveNetworkInfo();
        //should check null because in airplane mode it will be null
        return (netInfo != null && netInfo.isConnected());
    }

    //function for button, which takes the user to the create account activity
    public void goCreateAccount(){
        Button createAccountButton = (Button) findViewById(R.id.createAccountButton);
        createAccountButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent i = new Intent(getApplicationContext(), createAccount.class);
                startActivity(i);
            }
        });

    }

    //function for button, which takes the user to the logged users activity
    public void goLoggerUsers(){
        Button goLoggedButton = (Button) findViewById(R.id.loggedUsersButton);
        goLoggedButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent i = new Intent(getApplicationContext(), selectUser.class);
                startActivity(i);
            }
        });
    }

    //function for button, which takes the user to the user guide activity
    public void goGuide(){
        Button goGuideButton = (Button) findViewById(R.id.guideButton);
        goGuideButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Intent i = new Intent(getApplicationContext(), userGuide.class);
                startActivity(i);
            }
        });
    }

}