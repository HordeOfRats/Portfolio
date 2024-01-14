package com.example.gnaw;

import static android.content.ContentValues.TAG;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;

//recieves a intent from the current recipe view activity
public class notifBroadReciever extends BroadcastReceiver {
    public notifBroadReciever() {
    }

    @Override
    public void onReceive(Context context, Intent intent) {
        Log.d(TAG, "Reached timer broadcast reciever");

        //create a new intent

        Intent intent1 = new Intent(context, notifService.class);

        Bundle bundle = intent.getExtras();
        if (bundle != null) {
            for (String key : bundle.keySet()) {
                Log.e(TAG, key + " : " + (bundle.get(key) != null ? bundle.get(key) : "NULL"));
            }
        }

        //re package the old intent extras into the new intent
        String loggedID = intent.getStringExtra("LOGGED_ID");
        String index = intent.getStringExtra("INDEX");
        String info = intent.getStringExtra("INFO");
        String recipeToRead = intent.getStringExtra("recDOC_ID");
        Log.d(TAG, "broad recipe :  " + recipeToRead);
        intent1.putExtra("LOGGED_ID", loggedID);
        intent1.putExtra("INDEX", index);
        intent1.putExtra("INFO", info);
        intent1.putExtra("recDOC_ID", recipeToRead);
        //start the notif service with the created intent
        context.startService(intent1);
    }
}