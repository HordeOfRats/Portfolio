package com.example.gnaw;

import androidx.appcompat.app.AppCompatActivity;

import android.os.Bundle;
import android.view.View;
import android.webkit.WebView;
import android.widget.Button;
//class to display the user guide
public class userGuide extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_user_guide);
        backButton();
        loadWepPage();
    }

    //function for the back button
    public void backButton(){
        Button backButton = (Button) findViewById(R.id.UGBackButton);
        backButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
    }

    //load the user guide, stored locally, to the web view
    public void loadWepPage(){
        WebView myWebView = (WebView) findViewById(R.id.myWebView);
        myWebView.loadUrl("file:///android_asset/userGuide.html");
    }
}