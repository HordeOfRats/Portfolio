package com.example.gnaw;

import androidx.appcompat.app.AppCompatActivity;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

//class for the home screen
public class HomeScreen extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_home_screen);
        setButtons();
        setGreet();
    }

    //set the greeting message, using data from the intent
    public void setGreet(){
        String loggedUser = getIntent().getStringExtra("LOGGED_USER");
        TextView greetText = (TextView) findViewById(R.id.greetText);
        greetText.setText("Hello "+loggedUser+"!");
    }


    //activate all buttons
    public void setButtons(){
        Button goRecipes = (Button) findViewById(R.id.viewRecipesButton);
        Button goCurRecipe = (Button) findViewById(R.id.currentRecipeButton);
        Button logoutButton = (Button) findViewById(R.id.logoutButton);
        Button goNewRecipe = (Button) findViewById(R.id.newRecipeButton);

        //button to go to view recipes activity
        goRecipes.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                String loggedID = getIntent().getStringExtra("LOGGED_ID");
                Intent i = new Intent(getApplicationContext(), RecipeSelect.class);
                //put the logged id as extra
                i.putExtra("LOGGED_ID", loggedID);
                startActivity(i);
            }
        });

        //button to go to current recipe activity
        goCurRecipe.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                String loggedID = getIntent().getStringExtra("LOGGED_ID");
                Intent i = new Intent(getApplicationContext(), currentRecipe.class);
                //put the logged id as extra
                i.putExtra("LOGGED_ID", loggedID);
                startActivity(i);
            }
        });

        //button to log out
        logoutButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });

        //button to go to new recipe activity
        goNewRecipe.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                String loggedID = getIntent().getStringExtra("LOGGED_ID");
                String loggedUser = getIntent().getStringExtra("LOGGED_USER");
                Intent i = new Intent(getApplicationContext(), newRecipe.class);
                //add id and username as extra
                i.putExtra("LOGGED_ID", loggedID);
                i.putExtra("LOGGED_USER", loggedUser);
                startActivity(i);
            }
        });
    }
}