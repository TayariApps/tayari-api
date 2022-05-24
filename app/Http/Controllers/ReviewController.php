<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\FoodReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function placeReviews(){
        return response()->json(Review::get(),200);
    }

    public function menuReviews(){
        return response()->json(FoodReview::get(),200);
    }

    public function bestReviewedPlaces(){
        $data = \App\Models\Place::has('reviewed')
                    ->withAvg('reviewed as reviewAverage', 'rating')
                    ->orderBy('reviewAverage', 'DESC')->take(4)->get();
        return \response()->json($data,200);
    }

    public function getPlaceReview($placeID){

        $reviews = DB::table('reviews')
                        ->join('places',function($join) use ($placeID){
                            $join->on('places.id','=','reviews.place_id')
                                ->where('reviews.place_id','=', $placeID);
                        })->join('users','users.id','=','reviews.user_id')
                        ->select(
                            'reviews.id as id',
                            'users.name as name',
                            'reviews.content as content',
                            'reviews.rating as rating',
                            'users.user_image as image',
                            'reviews.created_at as date'
                            )->get();

        return \response()->json($reviews,200);
    }

    public function getMenuReview($menuID){
        return \response()->json(FoodReview::where('menu_id', $MenuID)->with('user')->get(),200);
    }

    public function storePlaceReview(Request $request){

        $validator = Validator::make($request->all(), [
            'place_id' => 'required', 
            'content' => 'required', 
            'rating' => 'required'
        ]);
 
        if ($validator->fails()) {
            return \response()->json('Enter all fields',400);
        }

        $checkIfUserAlreadyPosted = Review::where([
            'user_id' => $request->user()->id,
            'place_id' => $request->place_id
            ])->exists();

        if($checkIfUserAlreadyPosted){
            return \response()->json('You already reviewed this place',200);
        }

        Review::create([
            'place_id' => $request->place_id, 
            'user_id' => $request->user()->id, 
            'content' => $request->content, 
            'rating' => $request->rating
        ]);

        return response()->json('Review added', 201);
    }

    public function storePlaceAndFoodReview(Request $request){

        $somedata = $request->input();
        $somedata = file_get_contents("php://input");
        $cont = json_decode($somedata);

        if($request->has('placeReview')){

            $validator = Validator::make($request->placeReview, [
                'place_id' => 'required', 
                'content' => 'required', 
                'rating' => 'required'
            ]);
     
            if ($validator->fails()) {
                return \response()->json('Enter all place fields',400);
            }

            Review::updateOrCreate([
                'place_id' => $cont->placeReview->place_id, 
                'user_id' => $request->user()->id
            ],[
                'content' => $cont->placeReview->content,
                'rating' => $cont->placeReview->rating
            ]);
        }


        if($request->has('foodReview')){

            $validator = Validator::make($request->foodReview, [
                'menu_id' => 'required', 
                'content' => 'required', 
                'rating' => 'required'
            ]);
     
            if ($validator->fails()) {
                return \response()->json('Enter all food fields',400);
            }

            FoodReview::updateOrCreate([
                'menu_id' => $cont->foodReview->menu_id, 
                'user_id' => $request->user()->id
            ],[
                'content' => $cont->foodReview->content,
                'rating' => $cont->foodReview->rating
            ]);
        }

        return response()->json('Review complete',200);
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
