package com.example.gnaw;

import static android.content.ContentValues.TAG;

import android.app.Service;
import android.content.Intent;
import android.os.IBinder;
import android.util.Log;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;

import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.OnFailureListener;
import com.google.android.gms.tasks.OnSuccessListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.firestore.FirebaseFirestore;
import com.google.firebase.firestore.QueryDocumentSnapshot;
import com.google.firebase.firestore.QuerySnapshot;

import java.util.Map;


//background service, which is used to update overall ratings for recipes on the database
public class updateRatingService extends Service {

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        //create a new thread
        Thread ratUpdate = new Thread(){
            public void run() {
                //while loop to repeatedly perform the update function
                while(true)
                {
                    try {
                        //perform the following code every 5 seconds
                        Thread.sleep(5000);
                        Log.d(TAG, "Thread running");

                        //connect to the firestore database
                        FirebaseFirestore db = FirebaseFirestore.getInstance();
                        //get all recipes from the database
                        db.collection("recipes")
                                .get()
                                .addOnCompleteListener(new OnCompleteListener<QuerySnapshot>() {
                                    @Override
                                    public void onComplete(@NonNull Task<QuerySnapshot> task) {
                                        for (QueryDocumentSnapshot document : task.getResult()){
                                            //calculate the new overall rating for the recipe
                                            Float newRating = Float.valueOf(0);
                                            //get the recipe id
                                            String docID = document.getId();
                                            //get the reviews for the recipe
                                            Map<String, Object> docData = document.getData();
                                            Map<String, Object> ratings = (Map) docData.get("reviews");
                                            //loop through reviews and calculate the mean
                                            for (Object value : ratings.values()) {
                                                Float ratingToAdd = Float.valueOf(value.toString());
                                                newRating = newRating + ratingToAdd;
                                            }
                                            newRating = (newRating / (ratings.size()));
                                            String newRatingString = String.format("%.2f", newRating);
                                            newRating = Float.valueOf(newRatingString);
                                            //call update rating by id, with the recipe id and the new rating
                                            updateRatingByID(docID, newRating);

                                        }
                                    }
                                });


                    } catch (InterruptedException e) {
                        e.printStackTrace();
                    }

                }

            }
        };
        ratUpdate.start();
        return START_STICKY;
    }


    @Override
    public void onDestroy() {
        Toast.makeText(this, "service done", Toast.LENGTH_SHORT).show();
    }

    @Nullable
    @Override
    public IBinder onBind(Intent intent) {
        return null;
    }

    public void updateRatings(String docID){

    }

    //function to update a recipes overall rating with a new one, given its id and a new value for the rating
    public void updateRatingByID(String documentID, Float newRating){
        //connect the the database and update the rating field for the given document id
        FirebaseFirestore db = FirebaseFirestore.getInstance();
        db.collection("recipes").document(documentID)
                .update("rating", newRating)
                .addOnSuccessListener(new OnSuccessListener<Void>() {
                    @Override
                    public void onSuccess(Void aVoid) {
                        Log.d(TAG, "DocumentSnapshot successfully written!");
                    }
                })
                .addOnFailureListener(new OnFailureListener() {
                    @Override
                    public void onFailure(@NonNull Exception e) {
                        Log.w(TAG, "Error writing document", e);
                    }
                });
    }

}
