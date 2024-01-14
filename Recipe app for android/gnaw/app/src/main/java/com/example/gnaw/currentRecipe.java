package com.example.gnaw;

import static android.content.ContentValues.TAG;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.NotificationCompat;
import androidx.core.app.TaskStackBuilder;

import android.app.AlarmManager;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RatingBar;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

import java.sql.Array;
import java.util.*;

import com.google.android.gms.tasks.*;
import com.google.firebase.firestore.*;

public class currentRecipe extends AppCompatActivity {
    //declare class variables
    public Map recipeIns;
    public Integer recIndex;
    public String recipeToRead;
    public Integer numOfInstructions;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_current_recipe);
        //activate buttons and start the get recipe id function
        recIndex = 0;
        getRecipeId();
        moveToNextIns();
        moveToPrevIns();
        exitButton();
        submitRating();

    }


    //ensures that restoration data is saved to shared preferences when the activity is paused
    public void onPause() {
        super.onPause();
        Log.d(TAG, "paused");
        //open shared preferences and update the stored rec index and recipe doc id
        SharedPreferences sharedPref = this.getPreferences(Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPref.edit();
        editor.putInt("curRecIndex", recIndex);
        editor.putString("curRecipe", recipeToRead);
        editor.commit();

    }

    //function to get the recipe which the user has currently selected, from its doc id
    public void getRecipeId() {
        FirebaseFirestore db = FirebaseFirestore.getInstance();
        //get the user id from intent
        String loggedID = getIntent().getStringExtra("LOGGED_ID");
        //connect to firebase and get the correct recipe id for the user
        db.collection("users")
                .document(loggedID)
                .get()
                .addOnCompleteListener(new OnCompleteListener<DocumentSnapshot>() {
                    @Override
                    public void onComplete(@NonNull Task<DocumentSnapshot> task) {
                        DocumentSnapshot document = task.getResult();
                        Map<String, Object> docData = document.getData();
                        //set the recipe to read, from the user document
                        recipeToRead = (docData.get("currentRecipe")).toString();
                        Log.d(TAG, "recipeToRead1: " + recipeToRead);
                        //check if we reached this page from a notification, or if there is valid data in shared preferences, and adjust the recipe to read if needed
                        checkNotifRedirect();
                        Log.d(TAG, "recipeToReadaltered: " + recipeToRead);
                        getRecipe();
                    }
                });
    }

    //get recipe instructions from a recipe id
    public void getRecipe(){
        Log.d(TAG, "recipeToRead2: " + recipeToRead);
        //connect to firestore and get the correct recipe document
        FirebaseFirestore db = FirebaseFirestore.getInstance();
        db.collection("recipes")
                .document(recipeToRead)
                .get()
                .addOnCompleteListener(new OnCompleteListener<DocumentSnapshot>() {
                    @Override
                    public void onComplete(@NonNull Task<DocumentSnapshot> task) {
                        DocumentSnapshot document = task.getResult();
                        Map<String, Object> docData = document.getData();
                        //set the class variables according to the data from firestore
                        recipeIns = (Map) docData.get("recipe");
                        numOfInstructions = recipeIns.size();

                        //populate the page with the correct instruction, and activate the timer button
                        setInstruction();
                        timerButton();
                    }
                });

    }

    //function to check if this activity is reached from a notification, and if not check the shared preferences
    public void checkNotifRedirect(){
        //check for a recieved intent containined "INDEX", meaning we came here from a notification
        Intent checkIntent = getIntent();
        Log.d(TAG, "in check notif");
        if (checkIntent.hasExtra("INDEX")) {

            Bundle bundle = checkIntent.getExtras();
            if (bundle != null) {
                for (String key : bundle.keySet()) {
                    Log.e(TAG, key + " : " + (bundle.get(key) != null ? bundle.get(key) : "NULL"));
                }
            }

            //se the recipe to read and index to the values from the notification intent
            Integer newRecIndex = Integer.parseInt(checkIntent.getStringExtra("INDEX"));
            Log.d(TAG, "check notif index: " + newRecIndex);
            recIndex = newRecIndex;

            String newRecipeToRead = checkIntent.getStringExtra("recDOC_ID");
            Log.d(TAG, "check notif recipe: " + newRecipeToRead);
            recipeToRead = newRecipeToRead;


        }
        else{
            //otherwise, check if there are valid values in shared preferences
            checkForMemIndex();
        }
    }
    //check if shared preference index value is for this recipe, by comparing stored recipe id with the recipe id from the user
    public void checkForMemIndex(){
        SharedPreferences sharedPref = this.getPreferences(Context.MODE_PRIVATE);
        if (sharedPref.contains("curRecipe")){
            if (sharedPref.getString("curRecipe", null).equals(recipeToRead)){
                //if they're equal, return the user to the page they were on previously, stored in shared preferences
                recIndex = (sharedPref.getInt("curRecIndex", 0));
            }
        }
    }

    //function to update the ui with the correct recipe information
    public void setInstruction(){

        //get all ui elements and set them to invisible initially, unless they're always there
        TextView instructionTextView = (TextView) findViewById(R.id.instructionTextView);
        TextView indexTextView = (TextView) findViewById(R.id.indexTextView);
        Button timerButton = (Button) findViewById(R.id.timerButton);
        Button previousButton = (Button) findViewById(R.id.previousButton);
        Button nextButton = (Button) findViewById(R.id.nextButton);
        Button rateButton = (Button) findViewById(R.id.ratingSumbitButton);
        RatingBar ratingBar = (RatingBar) findViewById(R.id.ratingBar);
        rateButton.setVisibility(View.INVISIBLE);
        ratingBar.setVisibility(View.INVISIBLE);
        timerButton.setVisibility(View.INVISIBLE);
        //set the edit texts text to null
        instructionTextView.setText(null);
        indexTextView.setText(null);
        //if were on the ingredients page (index 0)
        if (recIndex.equals(0)){
            indexTextView.setText("Ing");
            previousButton.setVisibility(View.INVISIBLE);
            nextButton.setVisibility(View.VISIBLE);
            ArrayList ingList = (ArrayList) recipeIns.get("0");
            //show the ingredients in text view
            for (int i=0; i < ingList.size(); i++){
                instructionTextView.append("-"+ ingList.get(i) +"\n");
            }
        }
        //if were on the rating page, after all other pages
        else if((recIndex+1)==numOfInstructions){
            //set rating ui to visible
            rateButton.setVisibility(View.VISIBLE);
            ratingBar.setVisibility(View.VISIBLE);
            indexTextView.setText(recIndex.toString());
            previousButton.setVisibility(View.VISIBLE);
            nextButton.setVisibility(View.INVISIBLE);
            ArrayList insList = (ArrayList) recipeIns.get(recIndex.toString());
            instructionTextView.setText(insList.get(0).toString());
            if ((boolean) (insList.get(1))){
                timerButton.setVisibility(View.VISIBLE);
                timerButton.setText("Set timer for " + insList.get(2).toString() + "minutes");

            }
            else{
                timerButton.setVisibility(View.INVISIBLE);
            }


        }
        //if were on an instruction page
        else{
            previousButton.setVisibility(View.VISIBLE);
            nextButton.setVisibility(View.VISIBLE);
            indexTextView.setText(recIndex.toString());
            ArrayList insList = (ArrayList) recipeIns.get(recIndex.toString());
            Log.d(TAG, "Array list: "+ insList);
            //show instructions in text view
            instructionTextView.setText(insList.get(0).toString());
            //display timer if needed
            if ((boolean) (insList.get(1))){
                timerButton.setVisibility(View.VISIBLE);
                timerButton.setText("Set timer for " + insList.get(2).toString() + "minutes");

            }
            else{
                timerButton.setVisibility(View.INVISIBLE);
            }
        }
    }

    //function for the next button, to move between pages by changing rec index and setting the instruction again
    public void moveToNextIns(){
        Button nextButton = (Button) findViewById(R.id.nextButton);
        nextButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                recIndex = recIndex + 1;
                setInstruction();
            }
        });

    }

    //function for the previous button, to move between pages by changing rec index and setting the instruction again
    public void moveToPrevIns(){
        Button prevButton = (Button) findViewById(R.id.previousButton);
        prevButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                recIndex = recIndex - 1;
                setInstruction();
            }
        });

    }

    //function for the exit button, to go back to home screen
    public void exitButton(){
        Button exitButton = (Button) findViewById(R.id.backButton);
        exitButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
    }

    //function for the timer button
    public void timerButton(){
        Button timerButton = (Button) findViewById(R.id.timerButton);
        timerButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //set notification when clicked
                ArrayList insList = (ArrayList) recipeIns.get(recIndex.toString());
                setNotificationOnAlarm(insList.get(0).toString(), Integer.parseInt(insList.get(2).toString()));
            }
        });
    }

    //function to submit a user rating on the last page
    public void submitRating(){
        String loggedID = getIntent().getStringExtra("LOGGED_ID");
        RatingBar ratingBar = (RatingBar) findViewById(R.id.ratingBar);
        Button rateButton = (Button) findViewById(R.id.ratingSumbitButton);
        rateButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Float rating = ratingBar.getRating();
                Log.d(TAG, "Num of stars: "+ rating);
                //update the firestore database
                FirebaseFirestore db = FirebaseFirestore.getInstance();
                db.collection("recipes").document(recipeToRead)
                        .update("reviews."+loggedID, rating);



            }
        });
    }

    //set the notification with values from the timer button
    public void setNotificationOnAlarm(String info, Integer time){
        Log.d(TAG, "Timer button pressed");
        //who is logged in
        String loggedID = getIntent().getStringExtra("LOGGED_ID");
        //what recipe are they on
        String recipeToSend = recipeToRead;

        //create intent
        Intent notifyIntent = new Intent(this,notifBroadReciever.class);
        notifyIntent.putExtra("LOGGED_ID", loggedID);
        notifyIntent.putExtra("INDEX", recIndex.toString());
        notifyIntent.putExtra("INFO", info);
        notifyIntent.putExtra("recDOC_ID", recipeToSend);
        //create pending intent
        PendingIntent pendingIntent = PendingIntent.getBroadcast
                (this, 0, notifyIntent, PendingIntent.FLAG_IMMUTABLE | PendingIntent.FLAG_UPDATE_CURRENT);
        //send the intent to the notif broadcast service after the specified time has surpassed
        AlarmManager alarmManager = (AlarmManager) getSystemService(ALARM_SERVICE);
        alarmManager.set(AlarmManager.RTC_WAKEUP, System.currentTimeMillis() + (time * 6 * 1000), pendingIntent);


    }
}