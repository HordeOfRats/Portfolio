package com.example.gnaw;

import androidx.room.Database;
import androidx.room.RoomDatabase;

//defines a room database for our local storage
@Database(entities = {User.class}, version = 1)
public abstract class LUDatabase extends RoomDatabase {
    public abstract UserDao userDao();
}
