package com.example.gnaw;

import static android.content.ContentValues.TAG;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;

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

public class RecipeSelect extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_recipe_select);
        refreshRecipes();
        backButton();

    }

    //function to populate the recipe linear layout from the database
    public void refreshRecipes() {

        ScrollView recScrollView = (ScrollView) findViewById(R.id.recipeScrollView);
        LinearLayout recLinearLayout = (LinearLayout) findViewById(R.id.recipeLinearLayout);
        recLinearLayout.removeAllViews();

        FirebaseFirestore db = FirebaseFirestore.getInstance();

        //get all recipes from firestore, ordered by rating
        db.collection("recipes")
                .orderBy("rating", Query.Direction.DESCENDING)
                .get()
                .addOnCompleteListener(new OnCompleteListener<QuerySnapshot>() {
                    @Override
                    public void onComplete(@NonNull Task<QuerySnapshot> task) {
                        for (QueryDocumentSnapshot document : task.getResult()){
                            Map<String, Object> recipeData = document.getData();
                            //get neccasary values from the recipe document to create a button
                            String recipeID = document.getId();
                            String recName = (recipeData.get("recipeName")).toString();
                            String recRating = (recipeData.get("rating")).toString();
                            String recMaker = (recipeData.get("maker")).toString();

                            //call the addrecipebutton function to create a button for the current recipe in the for loop
                            addRecipeButton(recipeID, recName, recRating, recMaker);

                        }

                    }
                });





    }
    //function to add a button to go to a recipe
    public void addRecipeButton(String recID, String recName, String recRating, String recMaker){
        ScrollView recScrollView = (ScrollView) findViewById(R.id.recipeScrollView);
        LinearLayout recLinearLayout = (LinearLayout) findViewById(R.id.recipeLinearLayout);

        //define our new button
        Button newRecipeButton = new Button(this);
        //add attributes
        newRecipeButton.setLayoutParams(new LinearLayout.LayoutParams(LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.WRAP_CONTENT));
        newRecipeButton.setText(recName + " by " + recMaker + " rated: " + recRating);
        newRecipeButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //onclick, call goRecipe, for the rec id stored in the button
                goRecipe(recID);
            }
        });
        recLinearLayout.addView(newRecipeButton);
    }

    //function to go to a recipe, called by a button click of the previous function
    public void goRecipe(String recID){
        //open the firestore database
        FirebaseFirestore db = FirebaseFirestore.getInstance();
        //get the user id
        String loggedID = getIntent().getStringExtra("LOGGED_ID");
        //uodate the users current recipe in the database
        db.collection("users")
                .document(loggedID)
                .update("currentRecipe", recID);

        Intent i = new Intent(getApplicationContext(), currentRecipe.class);
        //put the user id as extra
        i.putExtra("LOGGED_ID", loggedID);
        //start the current recipe activity
        startActivity(i);
        //quit the recipe select activity
        finish();

    }

    //button to go back to the home screen
    public void backButton(){
        Button backButton = (Button) findViewById(R.id.RLBackButton);
        backButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
    }
}