<?php

static::addNamespace('apis/');
static::addNamespace('cp/services/');
static::addNamespace('cp/');
static::addNamespace('services');
static::addNamespace('crons');
static::match(['get'], 'profile/{name}/{id}', 'Dashboard@profile');
static::any('profile/jobs-in-{city}', 'Dashboard@profile');
static::get('profile/jobs-in-{city}', 'Dashboard@profile');
static::post('kaiser/post', 'Home@post');
