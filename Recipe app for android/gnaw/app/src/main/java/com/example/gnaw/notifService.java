package com.example.gnaw;

import static android.content.ContentValues.TAG;

import android.app.IntentService;
import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;

import androidx.core.app.NotificationCompat;
import androidx.core.app.NotificationManagerCompat;

public class notifService extends IntentService {
    //set our notification id
    private static final int NOTIFICATION_ID = 3;

    public notifService() {
        super("notifService");
    }

    @Override
    protected void onHandleIntent(Intent intent) {
        //create an android notification channel
        createNotificationChannel();

        //log the recieved intent values for debugging
        Bundle bundle = intent.getExtras();
        if (bundle != null) {
            for (String key : bundle.keySet()) {
                Log.e(TAG, key + " : " + (bundle.get(key) != null ? bundle.get(key) : "NULL"));
            }
        }


        Log.d(TAG, "Reached timer service");
        //get the extra values from the intent
        String loggedID = intent.getStringExtra("LOGGED_ID");
        Integer index = Integer.parseInt(intent.getStringExtra("INDEX"));
        String info = intent.getStringExtra("INFO");
        String recipeToRead = intent.getStringExtra("recDOC_ID");

        //create an intent for our notification
        Intent notifyIntent = new Intent(this, currentRecipe.class);
        notifyIntent.putExtra("recDOC_ID", recipeToRead);
        notifyIntent.putExtra("LOGGED_ID", loggedID);
        notifyIntent.putExtra("INDEX", index.toString());
        Log.d(TAG, "service recipe :  " + recipeToRead);
        //create a new pending intent for the notification
        PendingIntent pendingIntent = PendingIntent.getActivity(this, 0, notifyIntent, PendingIntent.FLAG_IMMUTABLE | PendingIntent.FLAG_UPDATE_CURRENT);


        //construct a notification using notification builder
        NotificationCompat.Builder builder = new NotificationCompat.Builder(this, getString(R.string.CHANNEL_ID))
                .setSmallIcon(R.drawable.rat_eating_cheese)
                .setContentTitle("Gnaw")
                .setContentText(info)
                .setPriority(NotificationCompat.PRIORITY_DEFAULT)
                .setContentIntent(pendingIntent)
                .setAutoCancel(true);

        NotificationManagerCompat notificationManager = NotificationManagerCompat.from(this);
        // notificationId is a unique int for each notification that you must define
        //send the notification
        notificationManager.notify(3, builder.build());

    }

    private void createNotificationChannel() {
        // Create the NotificationChannel, but only on API 26+ because
        // the NotificationChannel class is new and not in the support library
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            CharSequence name = getString(R.string.channel_name);
            String description = getString(R.string.channel_description);
            int importance = NotificationManager.IMPORTANCE_DEFAULT;
            NotificationChannel channel = new NotificationChannel(getString(R.string.CHANNEL_ID), name, importance);
            channel.setDescription(description);
            // Register the channel with the system; you can't change the importance
            // or other notification behaviors after this
            NotificationManager notificationManager = getSystemService(NotificationManager.class);
            notificationManager.createNotificationChannel(channel);
        }
    }
}