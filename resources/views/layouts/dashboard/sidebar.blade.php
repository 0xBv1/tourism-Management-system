<!-- Page Sidebar Start-->
<div class="page-sidebar">
    <div class="main-header-left d-none d-lg-block">
        <div class="logo-wrapper">
            <a href="{{ route('dashboard') }}">
                <img class="d-none d-lg-block blur-up lazyloaded sidebar-logo" src="{{ logo() }}" alt="">
            </a>
        </div>
    </div>
    <div class="sidebar custom-scrollbar">
        <a href="javascript:void(0)" class="sidebar-back d-lg-none d-block">
            <i class="fa fa-times" aria-hidden="true"></i>
        </a>
        <div class="sidebar-user">
            <img class="img-60" src="{{ asset('assets/admin/images/logo/sun-icon.png') }}" alt="#">
            <div>
                <h6 class="f-14">{{ admin()->first_name }}</h6>
                <p>{{ admin()->role }}</p>
            </div>
        </div>
        <ul class="sidebar-menu">
            
            @if(!auth()->user()->isSupplier())
                <x-dashboard.sidebar.single-link title="Dashboard" link="{{ route('dashboard') }}" icon="home"/>
            @endif
            
            {{-- Supplier Menu Items --}}
            @if(auth()->user()->isSupplier())
                <x-dashboard.sidebar.single-link title="Supplier Dashboard" link="{{ route('supplier.dashboard') }}" icon="home" />
                
                <x-dashboard.sidebar.single-link title="Company Profile" link="{{ route('supplier.profile.show') }}" icon="user" :permissions="['supplier.profile.view']" />
                
                <x-dashboard.sidebar.single-link title="Wallet" link="{{ route('supplier.wallet.index') }}" icon="credit-card" :permissions="['view_supplier_wallet']" />
                
                <x-dashboard.sidebar.link-with-child
                    title="Services"
                    icon="package"
                    :permissions="['supplier.hotels.list','supplier.tours.list','supplier.trips.list','supplier.transports.list','supplier.rooms.list']"
                    :children="[
                        ['title' => 'Hotels', 'link' => route('supplier.hotels.index'), 'permissions' => ['supplier.hotels.list'] ],
                        ['title' => 'Rooms', 'link' => route('supplier.rooms.index'), 'permissions' => ['supplier.rooms.list'] ],
                        ['title' => 'Tours', 'link' => route('supplier.tours.index'), 'permissions' => ['supplier.tours.list'] ],
                        ['title' => 'Trips', 'link' => route('supplier.trips.index'), 'permissions' => ['supplier.trips.list'] ],
                        ['title' => 'Transport', 'link' => route('supplier.transports.index'), 'permissions' => ['supplier.transports.list'] ],
]"
                />
                <x-dashboard.sidebar.link-with-child
                    title="Create Services"
                    icon="package"
                    :permissions="['supplier.hotels.create','supplier.tours.create','supplier.trips.create','supplier.transports.create','supplier.rooms.create']"
                    :children="[
                        ['title' => 'Create Hotel', 'link' => route('supplier.hotels.create'), 'permissions' => ['supplier.hotels.create'] ],
                        ['title' => 'Create Room', 'link' => route('supplier.rooms.create'), 'permissions' => ['supplier.rooms.create'] ],
                        ['title' => 'Create Tour', 'link' => route('supplier.tours.create'), 'permissions' => ['supplier.tours.create'] ],
                        ['title' => 'Create Trip', 'link' => route('supplier.trips.create'), 'permissions' => ['supplier.trips.create'] ],
                        ['title' => 'Create Transport', 'link' => route('supplier.transports.create'), 'permissions' => ['supplier.transports.create'] ],
                    ]"
                />
                <x-dashboard.sidebar.link-with-child
                    title="Statistics"
                    icon="bar-chart-2"
                    :permissions="['supplier.statistics.view']"
                    :children="[
                        ['title' => 'Overview', 'link' => route('supplier.statistics.index'), 'permissions' => ['supplier.statistics.view'] ],
                    ]"
                />
                
                <li class="sidebar-list">
                    <hr style="margin: 10px 20px; border-color: #e0e0e0;">
                </li>
            @endif

            <x-dashboard.sidebar.single-link title="Media" class="open-media" link="javascript:;" icon="camera" :permissions="['media.access']" />

            <x-dashboard.sidebar.single-link title="Sitemap" class="generate-sitemap" link="{{ route('dashboard.sitemap.generate') }}" icon="download" :permissions="['media.access']" />

            <x-dashboard.sidebar.link-with-child
                title="Redirect Rules"
                icon="rotate-cw"
                :permissions="['redirect-rules.list','redirect-rules.create','redirect-rules.edit','redirect-rules.delete']"
                :children="[
                    ['title' => 'Redirect Rules', 'link' => route('dashboard.redirect-rules.index'), 'permissions' => ['redirect-rules.list','redirect-rules.edit','redirect-rules.delete'] ],
                    ['title' => 'Create Redirect Rule', 'link' => route('dashboard.redirect-rules.create'), 'permissions' => ['redirect-rules.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Users"
                icon="user"
                :permissions="['users.list','users.create','users.edit','users.delete']"
                :children="[
                    ['title' => 'Users', 'link' => route('dashboard.users.index'), 'permissions' => ['users.list','users.edit','users.delete'] ],
                    ['title' => 'Create User', 'link' => route('dashboard.users.create'), 'permissions' => ['users.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
    title="Suppliers"
    icon="users"
    :permissions="['suppliers.list','suppliers.create','suppliers.edit','suppliers.delete']"
    :children="[
        ['title' => 'Suppliers', 'link' => route('dashboard.suppliers.index'), 'permissions' => ['suppliers.list','suppliers.edit','suppliers.delete'] ],
        ['title' => 'Create Supplier', 'link' => route('dashboard.suppliers.create'), 'permissions' => ['suppliers.create'] ],
    ]"
/>

            <x-dashboard.sidebar.link-with-child
                title="Supplier Services"
                icon="package"
                :permissions="['supplier-services.list']"
                :children="[
                    ['title' => 'All Services', 'link' => route('dashboard.supplier-services.index'), 'permissions' => ['supplier-services.list'] ],
                ]"
            />
            
            <x-dashboard.sidebar.single-link 
                title="Service Approvals" 
                link="{{ route('dashboard.service-approvals.index') }}" 
                icon="check-circle" 
                :permissions="['service-approvals.list']" 
            />
            <x-dashboard.sidebar.link-with-child
                title="Roles"
                icon="users"
                :permissions="['roles.list','roles.create','roles.edit','roles.delete']"
                :children="[
                    ['title' => 'Roles', 'link' => route('dashboard.roles.index'), 'permissions' => ['roles.list','roles.edit','roles.delete'] ],
                    ['title' => 'Create Role', 'link' => route('dashboard.roles.create'), 'permissions' => ['roles.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Clients"
                icon="user-check"
                :permissions="['clients.list','clients.create','clients.edit','clients.delete']"
                :children="[
                    ['title' => 'Clients', 'link' => route('dashboard.clients.index'), 'permissions' => ['clients.list','clients.edit','clients.delete'] ],
                    ['title' => 'Create Client', 'link' => route('dashboard.clients.create'), 'permissions' => ['clients.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Coupons"
                icon="tag"
                :permissions="['coupons.list','coupons.create','coupons.edit','coupons.delete']"
                :children="[
                    ['title' => 'Coupons', 'link' => route('dashboard.coupons.index'), 'permissions' => ['coupons.list','coupons.edit','coupons.delete'] ],
                    ['title' => 'Create Coupon', 'link' => route('dashboard.coupons.create'), 'permissions' => ['coupons.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Destinations"
                icon="globe"
                :permissions="['destinations.list','destinations.create','destinations.edit','destinations.delete']"
                :children="[
                    ['title' => 'Destinations', 'link' => route('dashboard.destinations.index'), 'permissions' => ['destinations.list','destinations.edit','destinations.delete'] ],
                    ['title' => 'Create Destination', 'link' => route('dashboard.destinations.create'), 'permissions' => ['destinations.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Categories"
                icon="book-open"
                :permissions="['categories.list','categories.create','categories.edit','categories.delete']"
                :children="[
                    ['title' => 'Categories', 'link' => route('dashboard.categories.index'), 'permissions' => ['categories.list','categories.edit','categories.delete'] ],
                    ['title' => 'Create Category', 'link' => route('dashboard.categories.create'), 'permissions' => ['categories.create'] ],
                ]"
            />



            <x-dashboard.sidebar.link-with-child
                title="Tours"
                icon="grid"
                :permissions="['tours.list','tours.create','tours.edit','tours.delete']"
                :children="[
                    ['title' => 'Tours', 'link' => route('dashboard.tours.index'), 'permissions' => ['tours.list','tours.edit','tours.delete'] ],
                    ['title' => 'Create Tour', 'link' => route('dashboard.tours.create'), 'permissions' => ['tours.create'] ],
                ]"
            />



            <x-dashboard.sidebar.link-with-child
                title="Transports"
                icon="truck"
                :permissions="['transports.list','transports.create','transports.edit','transports.delete']"
                :children="[
                    ['title' => 'All Transports', 'link' => route('dashboard.transports.index'), 'permissions' => ['transports.list','transports.edit','transports.delete'] ],
                    ['title' => 'Create Transport', 'link' => route('dashboard.transports.create'), 'permissions' => ['transports.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Trips & Bookings"
                icon="truck"
                :permissions="['trips.list','trips.create','trips.edit','trips.delete','trip-bookings.list','trip-bookings.create']"
                :children="[
                    ['title' => 'All Trips', 'link' => route('dashboard.trips.index'), 'permissions' => ['trips.list','trips.edit','trips.delete'] ],
                    ['title' => 'Create Trip', 'link' => route('dashboard.trips.create'), 'permissions' => ['trips.create'] ],
                    ['title' => 'Trip Bookings', 'link' => route('dashboard.trip-bookings.index'), 'permissions' => ['trip-bookings.list'] ],
                    ['title' => 'Create Booking', 'link' => route('dashboard.trip-bookings.create'), 'permissions' => ['trip-bookings.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Tour Options"
                icon="check-square"
                :permissions="['tour-options.list','tour-options.create','tour-options.edit','tour-options.delete']"
                :children="[
                    ['title' => 'Tour Options', 'link' => route('dashboard.tour-options.index'), 'permissions' => ['tour-options.list','tour-options.edit','tour-options.delete'] ],
                    ['title' => 'Create Tour Option', 'link' => route('dashboard.tour-options.create'), 'permissions' => ['tour-options.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Currencies"
                icon="dollar-sign"
                :permissions="['currencies.list','currencies.create','currencies.edit','currencies.delete']"
                :children="[
                    ['title' => 'Currencies', 'link' => route('dashboard.currencies.index'), 'permissions' => ['currencies.list','currencies.edit','currencies.delete'] ],
                    ['title' => 'Create Currency', 'link' => route('dashboard.currencies.create'), 'permissions' => ['currencies.create'] ],
                ]"
            />


            <x-dashboard.sidebar.single-link :permissions="['bookings.list']" title="Bookings" link="{{ route('dashboard.bookings.index') }}" icon="activity" />

            <x-dashboard.sidebar.link-with-child
                title="Custom Trips"
                icon="command"
                :permissions="['custom-trips.list']"
                :children="[
                    ['title' => 'All Requests', 'link' => route('dashboard.custom-trips.index'), 'permissions' => ['custom-trips.list'] ],
                    ['title' => 'Categories', 'link' => route('dashboard.customized-trip-categories.index'), 'permissions' => ['customized-trip-categories.list'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Pages"
                icon="layout"
                :permissions="['pages.list','pages.create','pages.edit','pages.delete']"
                :children="[
                    ['title' => 'Pages', 'link' => route('dashboard.pages.index'), 'permissions' => ['pages.list','pages.edit','pages.delete'] ],
                    ['title' => 'Create Page', 'link' => route('dashboard.pages.create'), 'permissions' => ['pages.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="FAQs"
                icon="layers"
                :permissions="['faqs.list','faqs.create','faqs.edit','faqs.delete']"
                :children="[
                    ['title' => 'FAQs', 'link' => route('dashboard.faqs.index'), 'permissions' => ['faqs.list','faqs.edit','faqs.delete'] ],
                    ['title' => 'Create FAQs', 'link' => route('dashboard.faqs.create'), 'permissions' => ['faqs.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Tour Reviews"
                icon="message-square"
                :permissions="['tour-reviews.list','tour-reviews.create','tour-reviews.edit','tour-reviews.delete']"
                :children="[
                    ['title' => 'Tour Reviews', 'link' => route('dashboard.tour-reviews.index'), 'permissions' => ['tour-reviews.list'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Contact Requests"
                icon="send"
                :permissions="['contact-requests.list']"
                :children="[
                    ['title' => 'Contact Requests', 'link' => route('dashboard.contact-requests.index'), 'permissions' => ['contact-requests.list'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Blogs"
                icon="book"
                :permissions="['blogs.list','blogs.create','blogs.edit','blogs.delete']"
                :children="[
                    ['title' => 'Blog Categories', 'link' => route('dashboard.blog-categories.index'), 'permissions' => ['blog-categories.list','blog-categories.edit','blog-categories.delete'] ],
                    ['title' => 'Blogs', 'link' => route('dashboard.blogs.index'), 'permissions' => ['blogs.list','blogs.edit','blogs.delete'] ],
                    ['title' => 'Create Blog', 'link' => route('dashboard.blogs.create'), 'permissions' => ['blogs.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Locations"
                icon="map-pin"
                :permissions="['locations.list','locations.create','locations.edit','locations.delete']"
                :children="[
                    ['title' => 'Locations', 'link' => route('dashboard.locations.index'), 'permissions' => ['locations.list','locations.edit','locations.delete'] ],
                    ['title' => 'Create Location', 'link' => route('dashboard.locations.create'), 'permissions' => ['locations.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Car Routes"
                icon="git-pull-request"
                :permissions="['car-routes.list','car-routes.create','car-routes.edit','car-routes.delete']"
                :children="[
                    ['title' => 'Car Routes', 'link' => route('dashboard.car-routes.index'), 'permissions' => ['car-routes.list','car-routes.edit','car-routes.delete'] ],
                    ['title' => 'Create Car Route', 'link' => route('dashboard.car-routes.create'), 'permissions' => ['car-routes.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Amenities"
                icon="thumbs-up"
                :permissions="['amenities.list','amenities.create','amenities.edit','amenities.delete']"
                :children="[
                    ['title' => 'Amenities', 'link' => route('dashboard.amenities.index'), 'permissions' => ['amenities.list','amenities.edit','amenities.delete'] ],
                    ['title' => 'Create Amenity', 'link' => route('dashboard.amenities.create'), 'permissions' => ['amenities.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Hotels"
                icon="sunrise"
                :permissions="['hotels.list','hotels.create','hotels.edit','hotels.delete']"
                :children="[
                    ['title' => 'Hotels', 'link' => route('dashboard.hotels.index'), 'permissions' => ['hotels.list','hotels.edit','hotels.delete'] ],
                    ['title' => 'Create Hotel', 'link' => route('dashboard.hotels.create'), 'permissions' => ['hotels.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Hotel Room Bookings"
                icon="calendar"
                :permissions="['hotel_room_bookings.list','hotel_room_bookings.create','hotel_room_bookings.edit','hotel_room_bookings.delete']"
                :children="[
                    ['title' => 'Hotel Room Bookings', 'link' => route('dashboard.hotel_room_bookings.index'), 'permissions' => ['hotel_room_bookings.list','hotel_room_bookings.edit','hotel_room_bookings.delete'] ],
                    ['title' => 'Create Booking', 'link' => route('dashboard.hotel_room_bookings.create'), 'permissions' => ['hotel_room_bookings.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Rooms"
                icon="square"
                :permissions="['rooms.list','rooms.create','rooms.edit','rooms.delete']"
                :children="[
                    ['title' => 'Rooms', 'link' => route('dashboard.rooms.index'), 'permissions' => ['rooms.list','rooms.edit','rooms.delete'] ],
                    ['title' => 'Create Room', 'link' => route('dashboard.rooms.create'), 'permissions' => ['rooms.create'] ],
                ]"
            />

{{--            <x-dashboard.sidebar.link-with-child--}}
{{--                title="Car Rentals"--}}
{{--                icon="activity"--}}
{{--                :permissions="['car-rentals.list','car-rentals.show']"--}}
{{--                :children="[--}}
{{--                    ['title' => 'Car Rentals', 'link' => route('dashboard.car-rentals.index'), 'permissions' => ['car-rentals.list','car-rentals.show'] ],--}}
{{--                ]"--}}
{{--            />--}}

            <x-dashboard.sidebar.single-link :permissions="['settings.show']" title="Settings" link="{{ route('dashboard.settings.show') }}" icon="settings" />






            {{--Sidebar Auto Generation--}}

            <x-dashboard.sidebar.single-link title="Logout" link="{{ route('logout') }}" icon="log-in" />

        </ul>
    </div>
</div>
<!-- Page Sidebar Ends-->
