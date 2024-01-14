package com.example.gnaw;

import androidx.annotation.NonNull;
import androidx.room.ColumnInfo;
import androidx.room.Entity;
import androidx.room.PrimaryKey;
//entity class for our room database, defining the columns we want to use
@Entity
public class User {
    @PrimaryKey
    @NonNull
    public String username;

    @ColumnInfo
    public String id;

}
