package com.example.gnaw;

import static android.content.ContentValues.TAG;

import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.Switch;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.lifecycle.ViewModelProvider;

import com.google.android.gms.tasks.OnFailureListener;
import com.google.android.gms.tasks.OnSuccessListener;
import com.google.firebase.firestore.DocumentReference;
import com.google.firebase.firestore.FirebaseFirestore;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

//class for creating a new recipe
public class newRecipe extends AppCompatActivity {
    //class variables
    public Map<String, Object> recipeIns = new HashMap<>();
    public Integer recIndex;
    public String recipeName;
    public ArrayList<String> ingredients = new ArrayList<String>();
    public ArrayList<ArrayList<Object>> instructions = new ArrayList<ArrayList<Object>>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_recipe);

        //see if data exists in the connected view class, meaning the activity is being recreated after a rotate
        newRecipeViewModel recMem = new ViewModelProvider(this).get(newRecipeViewModel.class);
        if (recMem.getMemRecIndex() == null){
            //if no data in view model, use default values
            recIndex = 0;
            recipeName = "";
        }
        else{
            //otherwise, restore the class variables using data from the view class
            recIndex = recMem.getMemRecIndex();
            setRecNameFromMem(recMem.getMemRecipeName());
            ingredients = recMem.getMemIngredients();
            instructions = recMem.getMemInstructions();
            //add ingredient buttons
            for (int i=0; i<ingredients.size(); i++){
                addIngButton(ingredients.get(i).toString());
            }
        }

        moveToNextIns();
        moveToPrevIns();
        exitButton();
        addIngredient();
        setInstruction();
        deleteButton();
        finishButton();
        //if were restoring this activity, set the entered values for the current page to the ones stored in view class
        if (recMem.getMemCurPage() != null){
            setPageFromMem(recMem.getMemCurPage());
        }

    }

    //function to restore the current page to the one stored in the view class for current page, incase the values were not saved to instructions before destruction
    public void setPageFromMem(ArrayList<Object> page){
        EditText instructionEditText = (EditText) findViewById(R.id.editTextInstruction);
        Switch timerSwitch = (Switch) findViewById(R.id.timerSwitch);
        EditText timerInput = (EditText) findViewById(R.id.editTextTime);
        instructionEditText.setText(page.get(0).toString());
        timerSwitch.setChecked(Boolean.parseBoolean(page.get(1).toString()));
        if ((Boolean.parseBoolean(page.get(1).toString())) == true){
            timerInput.setText(page.get(2).toString());
        }
        else{
            timerInput.setText("");
        }

    }

    //function to save current values to the view class upon a pause being detected, so that they can be restored
    protected void onPause() {
        super.onPause();

        //get the view class
        newRecipeViewModel recMem = new ViewModelProvider(this).get(newRecipeViewModel.class);
        EditText recipeNameEditText = (EditText) findViewById(R.id.recipeNameEditText);
        //use the view class functions to set its stored values
        recMem.setMemRecIndex(recIndex);
        recMem.setMemRecipeName(recipeNameEditText.getText().toString().trim());
        recMem.setMemIngredients(ingredients);
        recMem.setMemInstructions(instructions);
        //store the values for the page the user is currently on in the view class
        //unless its the ingredient page, where it isn't needed
        if (recIndex != 0){
            ArrayList<Object> newCurPage = new ArrayList<Object>();
            EditText instructionEditText = (EditText) findViewById(R.id.editTextInstruction);
            Switch timerSwitch = (Switch) findViewById(R.id.timerSwitch);
            EditText timerInput = (EditText) findViewById(R.id.editTextTime);

            newCurPage.add(instructionEditText.getText().toString());
            newCurPage.add((timerSwitch.isChecked()));
            if (timerSwitch.isChecked()){
                if (timerInput.getText().toString().equals("")){
                    newCurPage.add(1);
                }
                else{
                    newCurPage.add(Integer.parseInt(timerInput.getText().toString()));
                }
            }
            recMem.setMemCurPage(newCurPage);
        }

    }



    //function to set the recipe name on the ui to the one stored in the view class
    public void setRecNameFromMem(String recNameFromMem){
        EditText recipeNameEditText = (EditText) findViewById(R.id.recipeNameEditText);
        recipeNameEditText.setText(recNameFromMem);
    }


    //function to generate a ui page, reading from the instructions array if moving to a page thats already created
    public void setInstruction(){
        //ui elements
        EditText instructionEditText = (EditText) findViewById(R.id.editTextInstruction);
        EditText recipeNameEditText = (EditText) findViewById(R.id.recipeNameEditText);
        Button finishButton = (Button) findViewById(R.id.finishButton);
        Button deleteButton = (Button) findViewById(R.id.deleteButton);
        Switch timerSwitch = (Switch) findViewById(R.id.timerSwitch);
        EditText timerInput = (EditText) findViewById(R.id.editTextTime);
        TextView indexTextView = (TextView) findViewById(R.id.indexTextView);
        Button previousButton = (Button) findViewById(R.id.previousButton);
        Button nextButton = (Button) findViewById(R.id.nextButton);
        LinearLayout ingredientLinear = (LinearLayout) findViewById(R.id.ingredientLinear);
        ScrollView ingredientScroll = (ScrollView) findViewById(R.id.ingredientScrollView);
        EditText ingredientEditText = (EditText) findViewById(R.id.ingredientEditText);
        TextView ingredientHelp = (TextView) findViewById(R.id.ingredientDeleteText);
        Button addIngredientButton = (Button) findViewById(R.id.addIngredientButton);
        //set all ui elements to invisible, unless they're always there
        recipeNameEditText.setVisibility(View.INVISIBLE);
        instructionEditText.setVisibility(View.INVISIBLE);
        finishButton.setVisibility(View.INVISIBLE);
        deleteButton.setVisibility(View.INVISIBLE);
        timerSwitch.setVisibility(View.INVISIBLE);
        timerInput.setVisibility(View.INVISIBLE);
        addIngredientButton.setVisibility(View.INVISIBLE);
        ingredientEditText.setVisibility(View.INVISIBLE);
        ingredientLinear.setVisibility(View.INVISIBLE);
        ingredientScroll.setVisibility(View.INVISIBLE);
        ingredientHelp.setVisibility(View.INVISIBLE);
        indexTextView.setText(null);

        //if were on the ingredients page
        if (recIndex.equals(0)){
            indexTextView.setText("Ing");
            //display the ingredients ui elements
            addIngredientButton.setVisibility(View.VISIBLE);
            ingredientEditText.setVisibility(View.VISIBLE);
            ingredientLinear.setVisibility(View.VISIBLE);
            ingredientScroll.setVisibility(View.VISIBLE);
            ingredientHelp.setVisibility(View.VISIBLE);
            previousButton.setVisibility(View.INVISIBLE);
            nextButton.setVisibility(View.VISIBLE);
            recipeNameEditText.setVisibility(View.VISIBLE);
        }
        //if were on an instruction page
        else{
            //show instruction ui elements
            previousButton.setVisibility(View.VISIBLE);
            nextButton.setVisibility(View.VISIBLE);
            indexTextView.setText(recIndex.toString());
            instructionEditText.setVisibility(View.VISIBLE);
            finishButton.setVisibility(View.VISIBLE);
            deleteButton.setVisibility(View.VISIBLE);
            timerSwitch.setVisibility(View.VISIBLE);
            timerInput.setVisibility(View.VISIBLE);
            //if were on a newly generated page, generate a blank instruction page
            if (recIndex > instructions.size()){
                instructionEditText.setText("");
                timerSwitch.setChecked(false);
                timerInput.setText("");
            }
            else{
                //otherwise, load the values from the instructions array
                instructionEditText.setText(instructions.get(recIndex-1).get(0).toString());
                timerSwitch.setChecked(Boolean.parseBoolean(instructions.get(recIndex-1).get(1).toString()));
                if (Boolean.parseBoolean(instructions.get(recIndex-1).get(1).toString())){
                    timerInput.setText(instructions.get(recIndex-1).get(2).toString());
                }
                else {
                    timerInput.setText("");
                }
            }
        }
    }

    //button to move to the next instruction
    public void moveToNextIns(){
        EditText recipeNameEditText = (EditText) findViewById(R.id.recipeNameEditText);
        Button nextButton = (Button) findViewById(R.id.nextButton);
        nextButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //if the page were currently on is ingredients
                if (recIndex == 0){
                    //check ingredients have been added
                    if (ingredients.isEmpty()){
                        Toast toast = Toast.makeText(getApplicationContext(),
                                " Please add an ingredient.",
                                Toast.LENGTH_SHORT);

                        toast.show();
                    }
                    //check a recipe name has been added
                    else if(recipeNameEditText.getText().toString().trim().equals("")){
                        Toast toast = Toast.makeText(getApplicationContext(),
                                " Please add a recipe name.",
                                Toast.LENGTH_SHORT);

                        toast.show();
                    }
                    //set the recipe name class variable, and then add 1 to the rec(ipe) index and call set instruction
                    else{
                        recipeName = recipeNameEditText.getText().toString();
                        recIndex = recIndex + 1;
                        setInstruction();
                    }
                }
                //if were on an instruction page
                else{
                    //call the function to check that the current page were on has valid and complete details
                    if (instructionsCheck()){
                        //create an array for the new instruction
                        ArrayList<Object> instructionToAdd = new ArrayList<Object>();
                        EditText instructionEditText = (EditText) findViewById(R.id.editTextInstruction);
                        Switch timerSwitch = (Switch) findViewById(R.id.timerSwitch);
                        EditText timerInput = (EditText) findViewById(R.id.editTextTime);

                        instructionToAdd.add(instructionEditText.getText().toString());
                        instructionToAdd.add((timerSwitch.isChecked()));
                        if (timerSwitch.isChecked()){
                            instructionToAdd.add(Integer.parseInt(timerInput.getText().toString()));
                        }

                        Log.d(TAG, "Current rec index: " + recIndex);
                        //add the new instruction to instructions:
                        //if were on the last page of current instructions
                        if (recIndex-1 == instructions.size()){
                            instructions.add(instructionToAdd);
                        }
                        //shouldnt be called, but here incase since it will not cause disruption
                        //if the instructions array is currently empty
                        else if(instructions.isEmpty()){
                            instructions.add(instructionToAdd);
                        }
                        //otherwise, we put the new instruction at a position in the instruction array according to the current recipe index
                        else{
                            instructions.set(recIndex-1, instructionToAdd);
                        }

                        //add 1 to the rec(ipe) index and call set instruction
                        recIndex = recIndex + 1;
                        setInstruction();
                    }
                    else{
                        Toast toast = Toast.makeText(getApplicationContext(),
                                " Please complete the current instruction.",
                                Toast.LENGTH_SHORT);

                        toast.show();

                    }

                }
            }
        });

    }

    //button to move to the next instruction
    public void moveToPrevIns(){
        Button prevButton = (Button) findViewById(R.id.previousButton);
        prevButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                // we're always on an instruction page if we can even call this function
                //check the current page is completed and valid
                if (instructionsCheck()){
                    //make an array for the instruction were adding
                    ArrayList<Object> instructionToAdd = new ArrayList<Object>();
                    EditText instructionEditText = (EditText) findViewById(R.id.editTextInstruction);
                    Switch timerSwitch = (Switch) findViewById(R.id.timerSwitch);
                    EditText timerInput = (EditText) findViewById(R.id.editTextTime);

                    instructionToAdd.add(instructionEditText.getText().toString());
                    instructionToAdd.add((timerSwitch.isChecked()));
                    if (timerSwitch.isChecked()){
                        instructionToAdd.add(Integer.parseInt(timerInput.getText().toString()));
                    }

                    Log.d(TAG, "Current rec index: " + recIndex);
                    //add the new instruction to instructions
                    if (recIndex-1 == instructions.size()){
                        instructions.add(instructionToAdd);
                    }
                    else if(instructions.isEmpty()){
                        instructions.add(instructionToAdd);
                    }
                    else{
                        instructions.set(recIndex-1, instructionToAdd);
                    }
                    //minus 1 from the rec(ipe) index and call set instruction
                    recIndex = recIndex - 1;
                    setInstruction();
                }
                else{
                    Toast toast = Toast.makeText(getApplicationContext(),
                            " Please complete the current instruction.",
                            Toast.LENGTH_SHORT);

                    toast.show();

                }


            }
        });

    }

    //function for the exit button, to go back to the homescreen. will also destroy the view class
    public void exitButton(){
        Button exitButton = (Button) findViewById(R.id.backButton);
        exitButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
    }

    //function for the add ingredient button
    public void addIngredient(){
        Button addIngButton = (Button) findViewById(R.id.addIngredientButton);
        EditText ingredientInput = (EditText) findViewById(R.id.ingredientEditText);

        addIngButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //get the ingredient to add from the edit text
                String ingredientToAdd = ingredientInput.getText().toString().trim();
                //check that its not blank
                if(ingredientToAdd.equals("")){
                    Toast toast = Toast.makeText(getApplicationContext(),
                            "Please enter an ingredient",
                            Toast.LENGTH_SHORT);

                    toast.show();
                }
                else {
                    //if ingredients isnt empty
                    if (ingredients != null) {
                        //check if the ingredient is already in the ingredients array
                        if (ingredients.contains(ingredientToAdd)) {
                            Toast toast = Toast.makeText(getApplicationContext(),
                                    ingredientToAdd + " is already in list.",
                                    Toast.LENGTH_SHORT);

                            toast.show();
                        } else {
                            //if it isnt, add it to ingredients and make a button for it
                            addIngButton(ingredientToAdd);
                            ingredients.add(ingredientToAdd);
                        }
                    } else {
                        //if ingredients is empty, add the new ingredient straight away and make a button for it
                        addIngButton(ingredientToAdd);
                        ingredients.add(ingredientToAdd);
                    }
                }
            }
        });
    }

    //function to check if user is online
    public boolean isOnline() {

        ConnectivityManager cm = (ConnectivityManager) this.getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo netInfo = cm.getActiveNetworkInfo();
        //should check null because in airplane mode it will be null
        return (netInfo != null && netInfo.isConnected());
    }

    //function to add a new ingredient button to the ingredient linear layout
    public void addIngButton(String ingredientToAdd){
        LinearLayout ingredientLinear = (LinearLayout) findViewById(R.id.ingredientLinear);
        //define our new button
        Button newIngredientButton = new Button(this);
        //set its attributes according to the values passed to the function
        newIngredientButton.setLayoutParams(new LinearLayout.LayoutParams(LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.WRAP_CONTENT));
        newIngredientButton.setText(ingredientToAdd);
        newIngredientButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //onclick that removes the button, and its related ingredient from ingredients
                view.setVisibility(View.GONE);
                ingredients.remove(ingredientToAdd);
            }
        });
        //add the button to the linear layout
        ingredientLinear.addView(newIngredientButton);
    }

    //function to check if a instruction page is valid and complete
    public boolean instructionsCheck(){
        boolean complete = true;
        //check we're not on the ingredients page
        if(recIndex !=0 ) {
            EditText instructionEditText = (EditText) findViewById(R.id.editTextInstruction);
            Switch timerSwitch = (Switch) findViewById(R.id.timerSwitch);
            EditText timerInput = (EditText) findViewById(R.id.editTextTime);
            //check if an instrcution has been entered
            if (instructionEditText.getText().toString().trim().equals("")) {
                complete = false;
            }
            //if instruction requires a timer, check a time has been entered
            else if (timerSwitch.isChecked()) {
                if (timerInput.getText().toString().trim().equals("")) {
                    complete = false;
                }
            }
        }
        else if(instructions.isEmpty()){
            complete = false;
        }
        return complete;
    }

    //function for the delete button, which removes the current instruction page
    public void deleteButton(){
        Button deleteButton = (Button) findViewById(R.id.deleteButton);
        deleteButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //no need for deletion if the page hasnt actually been saved to the instructions array yet
                if (instructions.size() != recIndex - 1){
                    instructions.remove(recIndex-1);
                }
                //put the rec index back 1, so we will always be on a valid page when we call set instruction
                recIndex = recIndex - 1;
                //inform the user of page deletion
                Toast toast = Toast.makeText(getApplicationContext(),
                        "Deleted page",
                        Toast.LENGTH_SHORT);
                Log.d(TAG, String.valueOf(instructions));
                toast.show();
                //call set instruction
                setInstruction();
            }
        });
    }

    //function for the finish button, which attempts to upload the recipe
    public void finishButton(){
        Button finishButton = (Button) findViewById(R.id.finishButton);
        finishButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //firstly, check if the user is online
                if(isOnline() == true) {
                    //check if ingredients have been added
                    if (ingredients.isEmpty()) {
                        Toast toast = Toast.makeText(getApplicationContext(),
                                "Please add some ingredients",
                                Toast.LENGTH_SHORT);
                        Log.d(TAG, String.valueOf(instructions));
                        toast.show();
                    //check that we have instructions, or atleast have a valid one on the current page
                    } else if (instructions.isEmpty() && instructionsCheck() == false) {
                        Toast toast = Toast.makeText(getApplicationContext(),
                                "Please add some instructions",
                                Toast.LENGTH_SHORT);
                        Log.d(TAG, String.valueOf(instructions));
                        toast.show();
                    //check if the current page has a valid instruction
                    } else if (instructionsCheck() == false) {
                        Toast toast = Toast.makeText(getApplicationContext(),
                                "Please complete the current instruction",
                                Toast.LENGTH_SHORT);
                        Log.d(TAG, String.valueOf(instructions));
                        toast.show();
                    //check that we have a recipe name
                    } else if (recipeName == "") {
                        Toast toast = Toast.makeText(getApplicationContext(),
                                "Please add a recipe name",
                                Toast.LENGTH_SHORT);
                        Log.d(TAG, String.valueOf(instructions));
                        toast.show();
                    } else {
                        //add the current instruction(if it is one) to the instructions array if its not there
                        if (recIndex > instructions.size()) {
                            ArrayList<Object> instructionToAdd = new ArrayList<Object>();
                            EditText instructionEditText = (EditText) findViewById(R.id.editTextInstruction);
                            Switch timerSwitch = (Switch) findViewById(R.id.timerSwitch);
                            EditText timerInput = (EditText) findViewById(R.id.editTextTime);

                            instructionToAdd.add(instructionEditText.getText().toString());
                            instructionToAdd.add((timerSwitch.isChecked()));
                            if (timerSwitch.isChecked()) {
                                instructionToAdd.add(Integer.parseInt(timerInput.getText().toString()));
                            }

                            instructions.add(instructionToAdd);
                        }
                        //connect to firebase
                        FirebaseFirestore db = FirebaseFirestore.getInstance();

                        //create an extra instruction for the rating page
                        ArrayList<Object> ratingSection = new ArrayList<Object>();
                        ratingSection.add("Please rate!");
                        ratingSection.add(false);


                        //create neccasary objects for the recipe document
                        Map<String, Object> newRecipe = new HashMap<>();
                        Map<String, Object> reviews = new HashMap<>();
                        String loggedUser = getIntent().getStringExtra("LOGGED_USER");
                        String loggedID = getIntent().getStringExtra("LOGGED_ID");
                        //put a default rating for the recipe of 3
                        reviews.put(loggedID, 3);

                        //add ingredients, then instructions and then the rating page to the recipeIns map
                        recipeIns.put("0", ingredients);
                        for (int i = 0; i < instructions.size(); i++) {
                            recipeIns.put(String.valueOf((i + 1)), instructions.get(i));
                        }
                        recipeIns.put(String.valueOf(instructions.size() + 1), ratingSection);

                        //put all of our created objects into a map representing the new recipe document
                        newRecipe.put("recipe", recipeIns);
                        newRecipe.put("maker", loggedUser);
                        newRecipe.put("rating", 3);
                        newRecipe.put("recipeName", recipeName);
                        newRecipe.put("reviews", reviews);

                        //add new recipe map as a new document in the "recipes" collection
                        db.collection("recipes")
                                .add(newRecipe)
                                .addOnSuccessListener(new OnSuccessListener<DocumentReference>() {
                                    @Override
                                    public void onSuccess(DocumentReference documentReference) {
                                        //inform the user of a successful upload
                                        Log.d(TAG, "DocumentSnapshot written with ID: " + documentReference.getId());
                                        Toast toast = Toast.makeText(getApplicationContext(),
                                                "Successfully submitted recipe",
                                                Toast.LENGTH_SHORT);
                                        Log.d(TAG, String.valueOf(instructions));
                                        toast.show();
                                        //exit the new recipe activity
                                        finish();
                                    }
                                })
                                .addOnFailureListener(new OnFailureListener() {
                                    @Override
                                    public void onFailure(@NonNull Exception e) {
                                        //inform the user of an error
                                        Log.w(TAG, "Error adding document", e);
                                        Toast toast = Toast.makeText(getApplicationContext(),
                                                "Error submitting recipe",
                                                Toast.LENGTH_SHORT);
                                        Log.d(TAG, String.valueOf(instructions));
                                        toast.show();
                                    }
                                });
                    }
                }
                else{
                    Toast toast = Toast.makeText(getApplicationContext(),
                            "Please connect to the internet",
                            Toast.LENGTH_SHORT);
                    toast.show();
                }
            }
        });


    }



}