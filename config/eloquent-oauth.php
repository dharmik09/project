<?php
use App\Teenagers;
return [
    'model' => Teenagers::class,
    'table' => 'oauth_identities',
    'providers' => [
        'facebook' => [
            'client_id' => '146901802433877',
            'client_secret' => 'ad4e84debd770e53cc3afc42a25bf1e8',
            'redirect_uri' => 'https://www.proteenlife.com/facebook/login',
            'scope' => ['public_profile'],
        ],
//        'facebook' => [
//            'client_id' => '864798093650034',
//            'client_secret' => '910a8e4fe2daf2e885b66a62345b983e',
//            'redirect_uri' => 'http://www.proteen.inexture.com/facebook/login',
//            'scope' => ['public_profile'],
//        ],
//          'facebook' => [
//            'client_id' => '100819853624778',
//            'client_secret' => '6e02b4d7fb21bf9092df792037753192',
//            'redirect_uri' => 'http://proteen2.inexture.com/facebook/login',
//            'scope' => ['public_profile'],
//        ],  
        'google' => [
            'client_id' => '462212200950-jom680gka0cjibu4qenps972rrs3vcu4.apps.googleusercontent.com',
            'client_secret' => 'MX67OUzkAlaTLC9gGixibXCa',
            'redirect_uri' => 'https://www.proteenlife.com/google/login',
            'scope' => [],
        ],
//        'google' => [
//            'client_id' => '774570931076-jpf39sglobmgi71pnl9c21drp4cf9uu2.apps.googleusercontent.com',
//            'client_secret' => '_cQ_DjZegIN-Y8Frk0SgXHrk',
//            'redirect_uri' => 'http://www.proteen.inexture.com/google/login',
//            'scope' => [],
//        ],
//        'google' => [
//            'client_id' => '305093710556-jks6ukaql0bfnfubkhkkcv9ek0t7aelc.apps.googleusercontent.com',
//            'client_secret' => 'fPn4G2DMk-E-lCoKNoOC1BP0',
//            'redirect_uri' => 'http://proteen2.inexture.com/google/login',
//            'scope' => [],
//        ],
        
    ],
];