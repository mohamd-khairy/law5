<?php

function userData()
{
    if(!empty($_SESSION['user'])){

        return $_SESSION['user'];
    }    

    return response()->json(['message' => 'Unauthorized.'], 401);
}