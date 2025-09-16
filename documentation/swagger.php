<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Tourism API Documentation",
 *     description="Comprehensive API documentation for the Tourism Management System",
 *     @OA\Contact(
 *         email="support@tourism-api.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Development Server"
 * )
 * 
 * @OA\Server(
 *     url="https://api.tourism.com/api",
 *     description="Production Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Trips",
 *     description="Trip management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="CustomTrips",
 *     description="Custom trip request endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Bookings",
 *     description="Booking management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Reviews",
 *     description="Trip review and rating endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Tour Reviews",
 *     description="Tour review and rating endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Wishlist",
 *     description="Wishlist management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Hotels",
 *     description="Hotel management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentication endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Categories",
 *     description="Category management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Destinations",
 *     description="Destination management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Blog",
 *     description="Blog management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Cart",
 *     description="Shopping cart endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Payments",
 *     description="Payment processing endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Cities",
 *     description="City management endpoints"
 * )
 */ 