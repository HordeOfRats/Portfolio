package com.example.gnaw;

import androidx.room.Dao;
import androidx.room.Delete;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import java.util.List;
//defines functions that we can call to interact with our room database
@Dao
public interface UserDao {
    @Query("SELECT * FROM User")
    List<User> getAll();


    @Insert(onConflict = OnConflictStrategy.REPLACE)
    public void insertAll(User... users);

    @Delete
    void delete(User user);
}
