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
                <h6 class="f-14">{{ admin()->first_name ?? 'Admin' }}</h6>
                <p>{{ admin()->getRoleNames()->first() ?? 'Administrator' }}</p>
            </div>
        </div>
        <ul class="sidebar-menu">
        
            <x-dashboard.sidebar.single-link title="Dashboard" link="{{ route('dashboard') }}" icon="home"/>

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
                title="Roles"
                icon="users"
                :permissions="['roles.list','roles.create','roles.edit','roles.delete']"
                :children="[
                    ['title' => 'Roles', 'link' => route('dashboard.roles.index'), 'permissions' => ['roles.list','roles.edit','roles.delete'] ],
                    ['title' => 'Create Role', 'link' => route('dashboard.roles.create'), 'permissions' => ['roles.create'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Inquiries"
                icon="message-circle"
                :permissions="['inquiries.list','inquiries.create','inquiries.edit','inquiries.delete','inquiries.show','inquiries.confirm']"
                :children="[
                    ['title' => 'All Inquiries', 'link' => route('dashboard.inquiries.index'), 'permissions' => ['inquiries.list','inquiries.edit','inquiries.delete','inquiries.show'] ],
                    ['title' => 'Create Inquiry', 'link' => route('dashboard.inquiries.create'), 'permissions' => ['inquiries.create'] ],
                ]"
            />

            <x-dashboard.sidebar.single-link 
                :permissions="['bookings.list','bookings.show']" 
                title="Bookings" 
                link="{{ route('dashboard.bookings.index') }}" 
                icon="file-text" 
            />

            <x-dashboard.sidebar.link-with-child
                title="Finance"
                icon="dollar-sign"
                :permissions="['payments.list','payments.create','payments.edit','payments.delete','payments.show','payments.statements','payments.aging-buckets']"
                :children="[
                    ['title' => 'Payments', 'link' => route('dashboard.payments.index'), 'permissions' => ['payments.list','payments.edit','payments.delete','payments.show'] ],
                    ['title' => 'Create Payment', 'link' => route('dashboard.payments.create'), 'permissions' => ['payments.create'] ],
                    ['title' => 'Payment Statements', 'link' => route('dashboard.payments.statements'), 'permissions' => ['payments.statements'] ],
                    ['title' => 'Aging Buckets', 'link' => route('dashboard.payments.aging-buckets'), 'permissions' => ['payments.aging-buckets'] ],
                ]"
            />

            <x-dashboard.sidebar.link-with-child
                title="Resource Management"
                icon="package"
                :permissions="['hotels.list','vehicles.list','guides.list','representatives.list','tickets.list','nile_cruises.list','dahabias.list','restaurants.list']"
                :children="[
                    ['title' => 'Hotels', 'link' => route('dashboard.hotels.index'), 'permissions' => ['hotels.list'] ],
                    ['title' => 'Vehicles', 'link' => route('dashboard.vehicles.index'), 'permissions' => ['vehicles.list'] ],
                    ['title' => 'Guides', 'link' => route('dashboard.guides.index'), 'permissions' => ['guides.list'] ],
                    ['title' => 'Representatives', 'link' => route('dashboard.representatives.index'), 'permissions' => ['representatives.list'] ],
                    ['title' => 'Tickets', 'link' => route('dashboard.tickets.index'), 'permissions' => ['tickets.list'] ],
                    ['title' => 'Nile Cruises', 'link' => route('dashboard.nile-cruises.index'), 'permissions' => ['nile_cruises.list'] ],
                    ['title' => 'Dahabias', 'link' => route('dashboard.dahabias.index'), 'permissions' => ['dahabias.list'] ],
                    ['title' => 'Restaurants', 'link' => route('dashboard.restaurants.index'), 'permissions' => ['restaurants.list'] ],
                    ['title' => 'Hotel Calendar', 'link' => route('dashboard.hotels.calendar'), 'permissions' => ['hotels.calendar'] ],
                    ['title' => 'Vehicle Calendar', 'link' => route('dashboard.vehicles.calendar'), 'permissions' => ['vehicles.calendar'] ],
                    ['title' => 'Guide Calendar', 'link' => route('dashboard.guides.calendar'), 'permissions' => ['guides.calendar'] ],
                    ['title' => 'Representative Calendar', 'link' => route('dashboard.representatives.calendar'), 'permissions' => ['representatives.calendar'] ],
                ]"
            />

<!-- <x-dashboard.sidebar.single-link 
                :permissions="['bookings.list','bookings.show']" 
                title="Resource Assignment" 
                link="{{ route('dashboard.bookings.index') }}#assign-resources" 
                icon="link-2" 
            />             -->

            <x-dashboard.sidebar.link-with-child
                title="Reports"
                icon="bar-chart-2"
                :permissions="['reports.index','reports.inquiries','reports.bookings','reports.finance','reports.operational','reports.performance']"
                :children="[
                    ['title' => 'Dashboard', 'link' => route('dashboard.reports.index'), 'permissions' => ['reports.index'] ],
                    ['title' => 'Inquiries Report', 'link' => route('dashboard.reports.inquiries'), 'permissions' => ['reports.inquiries'] ],
                    ['title' => 'Bookings Report', 'link' => route('dashboard.reports.bookings'), 'permissions' => ['reports.bookings'] ],
                    ['title' => 'Finance Report', 'link' => route('dashboard.reports.finance'), 'permissions' => ['reports.finance'] ],
                    ['title' => 'Operational Report', 'link' => route('dashboard.reports.operational'), 'permissions' => ['reports.operational'] ],
 ]"
            />
<!--                    ['title' => 'Performance Report', 'link' => route('dashboard.reports.performance'), 'permissions' => ['reports.performance'] ],
                    ['title' => 'Resource Utilization', 'link' => route('dashboard.reports.resource-utilization'), 'permissions' => ['resource-reports.index'] ],
                 -->
            <x-dashboard.sidebar.single-link :permissions="['settings.show']" title="Settings" link="{{ route('dashboard.settings.show') }}" icon="settings" />

            {{--Sidebar Auto Generation--}}

            <x-dashboard.sidebar.single-link title="Logout" link="{{ route('logout') }}" icon="log-in" />

        </ul>
    </div>
</div><!-- Page Sidebar Ends-->