package com.example.gnaw;

import androidx.lifecycle.ViewModel;

import java.util.ArrayList;
//view model new recipe activity
public class newRecipeViewModel extends ViewModel {
    //private class variables
    private Integer memRecIndex = null;
    private String memRecipeName = null;
    private ArrayList<String> memIngredients = null;
    private ArrayList<ArrayList<Object>> memInstructions = null;
    private ArrayList<Object> memCurPage = null;

    //set the values in the view model to store
    public void setMemRecIndex(Integer newRecIndex) {
        memRecIndex = (newRecIndex);
    }

    public void setMemRecipeName(String newRecipeName) {
        memRecipeName = (newRecipeName);
    }

    public void setMemIngredients(ArrayList<String> newIngredients) {
        memIngredients = (newIngredients);
    }

    public void setMemInstructions(ArrayList<ArrayList<Object>> newInstructions) {
        memInstructions = (newInstructions);
    }

    public void setMemCurPage(ArrayList<Object> newCurPage) {
        memCurPage = (newCurPage);
    }

    //get stored values from the view model

    public Integer getMemRecIndex() {
        return memRecIndex;
    }

    public String getMemRecipeName() {
        return memRecipeName;
    }

    public ArrayList<String> getMemIngredients() {
        return memIngredients;
    }

    public ArrayList<ArrayList<Object>> getMemInstructions() {
        return memInstructions;
    }

    public ArrayList<Object> getMemCurPage() {
        return memCurPage;
    }

}
