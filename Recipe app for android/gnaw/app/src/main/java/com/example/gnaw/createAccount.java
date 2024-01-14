package com.example.gnaw;

import static android.content.ContentValues.TAG;

import android.content.Intent;
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

import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.OnFailureListener;
import com.google.android.gms.tasks.OnSuccessListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.firestore.DocumentReference;
import com.google.firebase.firestore.FirebaseFirestore;
import com.google.firebase.firestore.QueryDocumentSnapshot;
import com.google.firebase.firestore.QuerySnapshot;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

//activity for creating new user accounts
public class createAccount extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_create_account);
        //activate the buttons
        goBack();
        checkAccountToMake();
    }

    //function for the back button
    public void goBack(){
        Button backButton = (Button) findViewById(R.id.CABackButton);

        backButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
    }

    //function for the confirm account button, which checks details entered
    public void checkAccountToMake(){
        //get the used edit texts and the button
        EditText usernameEdit = (EditText) findViewById(R.id.createUserEditText);
        EditText passwordEdit = (EditText) findViewById(R.id.createPasswordEditText);
        Button createAccountButton = (Button) findViewById(R.id.confirmAccountButton);

        createAccountButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //put entered strings into local variables
                String password = passwordEdit.getText().toString().trim();
                String username = usernameEdit.getText().toString().trim();
                FirebaseFirestore db = FirebaseFirestore.getInstance();
                //check if the username exists in firebase
                db.collection("users")
                        .whereEqualTo("username", username)
                        .get()
                        .addOnCompleteListener(new OnCompleteListener<QuerySnapshot>() {
                            @Override
                            public void onComplete(@NonNull Task<QuerySnapshot> task) {
                                if (task.isSuccessful()) {
                                    if (task.getResult().isEmpty()){
                                        //if the username does not exist, add the new user to the firebase, as long as the password is valid
                                        if (password.length() > 2){
                                            Map<String, Object> newUser = new HashMap<>();
                                            newUser.put("username", username);
                                            newUser.put("password", password);
                                            newUser.put("currentRecipe", "M4IUTUga272VLXWAkpjP");
                                            db.collection("users")
                                                    .add(newUser)
                                                    .addOnSuccessListener(new OnSuccessListener<DocumentReference>() {
                                                        @Override
                                                        public void onSuccess(DocumentReference documentReference) {
                                                            Toast toast = Toast.makeText(getApplicationContext(),
                                                                    "Successfully created account",
                                                                    Toast.LENGTH_SHORT);
                                                            toast.show();
                                                            finish();
                                                        }
                                                    })
                                                    .addOnFailureListener(new OnFailureListener() {
                                                        @Override
                                                        public void onFailure(@NonNull Exception e) {
                                                            Toast toast = Toast.makeText(getApplicationContext(),
                                                                    "Error creating account",
                                                                    Toast.LENGTH_SHORT);
                                                            toast.show();

                                                        }
                                                    });
                                        }
                                        else{
                                            Toast toast = Toast.makeText(getApplicationContext(),
                                                    "Invalid password",
                                                    Toast.LENGTH_SHORT);
                                            toast.show();
                                        }
                                    }
                                    else{
                                        Toast toast = Toast.makeText(getApplicationContext(),
                                                "Username already exists!",
                                                Toast.LENGTH_SHORT);
                                        toast.show();
                                    }


                                } else {
                                    Log.d(TAG, "Error getting documents: ", task.getException());
                                }
                            }
                        });
            }
        });

    }

}