<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\FoodReview;
use App\Models\Place;
use App\Models\Menu;

class ReviewController extends Controller
{
    public function placeReviews(){
        return response()->json(Review::get(),200);
    }

    public function menuReviews(){
        return response()->json(FoodReview::get(),200);
    }

    public function getPlaceReview($placeID){
        $place = Place::where('id', $placeID)->with('reviews')->get();
        return \response($place,200);
    }

    public function getMenuReview($menuID){
        $menu = Menu::where('id', $menuID)->with('reviews')->get();
        return \response($menu,200);
    }

    public function storePlaceReview(Request $request){
        Review::create([
            'place_id' => $request->place_id, 
            'user_id' => $request->user()->id, 
            'content' => $request->content, 
            'rating' => $request->rating
        ]);

        return response()->json('Review added', 201);
    }

    public function storeFoodReview(Request $request){
        FoodReview::create([
            'menu_id' => $request->menu_id, 
            'user_id' => $request->user()->id, 
            'content' => $request->content, 
            'rating' => $request->rating
        ]);

        return response()->json('Review added', 201);
    }
}
