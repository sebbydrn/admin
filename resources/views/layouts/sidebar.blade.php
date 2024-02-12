<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-success elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link navbar-success">
        <img src="{{asset('public/images/logo4.png')}}" alt="RSIS Logo" class="brand-image elevation-3"
        style="opacity: 1; background-color: white;">
        <span class="brand-text font-weight-light">RSIS Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                {{-- <img src="{{asset('public/assets/AdminLTE-3.0.0/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image"> --}}
                <?php $name = Auth::user()->firstname . ' ' . Auth::user()->lastname; ?>
                <img src="{{ Avatar::create($name)->toBase64() }}" />
            </div>
            <div class="info">
                <a href="#" class="d-block">{{Auth::user()->firstname . ' ' . Auth::user()->lastname}}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                
                @permission('view_user_management_dashboard')
                <li class="nav-item">
                    <a href="{{url('/')}}" class="nav-link {{Request::segment(1) == '' ? 'active' : ''}}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @endpermission
                
                @ability('', 'view_user, view_role, view_permission')
                <li class="nav-item has-treeview {{Request::segment(1) == 'users' || Request::segment(1) == 'roles' || Request::segment(1) == 'permissions' || Request::segment(1) == 'pending_registrations' ? 'menu-open' : ''}}">
                    <a href="#" class="nav-link {{Request::segment(1) == 'users' || Request::segment(1) == 'roles' || Request::segment(1) == 'permissions' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            User Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @permission('view_user')
                        <li class="nav-item">
                            <a href="{{url('users')}}" class="nav-link {{Request::segment(1) == 'users' ? 'active' : ''}}">
                                <i class="fas fa-circle nav-icon"></i>
                                <p>Users</p>
                            </a>
                        </li>
                        @endpermission

                        @permission('view_role')
                        <li class="nav-item">
                            <a href="{{url('roles')}}" class="nav-link {{Request::segment(1) == 'roles' ? 'active' : ''}}">
                                <i class="fas fa-circle nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                        @endpermission
                        
                        @permission('view_permission')
                        <li class="nav-item">
                            <a href="{{url('permissions')}}" class="nav-link {{Request::segment(1) == 'permissions' ? 'active' : ''}}">
                                <i class="fas fa-circle nav-icon"></i>
                                <p>Permissions</p>
                            </a>
                        </li>
                        @endpermission

                        <li class="nav-item">
                            <a href="{{route('user_monitor.last_login')}}" class="nav-link {{Request::segment(1) == 'user_monitor' ? 'active' : ''}}">
                                <i class="fas fa-circle nav-icon"></i>
                                <p>Users Last Login</p>
                            </a>
                        </li>
                        
                        @permission('view_pending_registration')
                        <li class="nav-item">
                            <a href="{{url('pending_registrations')}}" class="nav-link {{Request::segment(1) == 'pending_registrations' ? 'active' : ''}}">
                                <i class="fas fa-inbox nav-icon"></i>
                                <p>Pending Registrations</p>
                            </a>
                        </li>
                        @endpermission

                        @permission('view_registration_data_monitoring')
                        <li class="nav-item">
                            <a href="{{url('user_data_monitoring')}}" class="nav-link {{Request::segment(1) == 'user_data_monitoring' ? 'active' : ''}}">
                                <i class="fas fa-tachometer-alt nav-icon"></i>
                                <p>Data Monitoring</p>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>
                @endability
                
                @permission('view_system')
                <li class="nav-item">
                    <a href="{{url('systems')}}" class="nav-link {{Request::segment(1) == 'systems' ? 'active' : ''}}">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>
                            Systems
                        </p>
                    </a>
                </li>
                @endpermission
                
                @permission('view_affiliation')
                <li class="nav-item">
                    <a href="{{url('affiliations')}}" class="nav-link {{Request::segment(1) == 'affiliations' ? 'active' : ''}}">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>
                            Affiliations
                        </p>
                    </a>
                </li>
                @endpermission
            
                @permission('view_philrice_station')
                <li class="nav-item">
                    <a href="{{url('philrice_stations')}}" class="nav-link {{Request::segment(1) == 'philrice_stations' ? 'active' : ''}}">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>
                            PhilRice Stations
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_activity')
                <li class="nav-item">
                    <a href="{{url('activities')}}" class="nav-link {{Request::segment(1) == 'actvities' ? 'active' : ''}}">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>
                            Activities
                        </p>
                    </a>
                </li>
                @endpermission
                
                @permission('view_page')
                <li class="nav-item">
                    <a href="{{url('pages')}}" class="nav-link {{Request::segment(1) == 'pages' ? 'active' : ''}}">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>
                            Pages
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_section')
                <li class="nav-item">
                    <a href="{{url('sections')}}" class="nav-link {{Request::segment(1) == 'sections' ? 'active' : ''}}">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>
                            Sections
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_content')
                <li class="nav-item">
                    <a href="{{url('contents')}}" class="nav-link {{Request::segment(1) == 'contents' ? 'active' : ''}}">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>
                            Contents
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_contact')
                <li class="nav-item">
                    <a href="{{url('contacts')}}" class="nav-link {{Request::segment(1) == 'contacts' ? 'active' : ''}}">
                        <i class="fas fa-address-book nav-icon"></i>
                        <p>
                            Contacts
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_link')
                <li class="nav-item">
                    <a href="{{url('links')}}" class="nav-link {{Request::segment(1) == 'links' ? 'active' : ''}}">
                        <i class="fas fa-link nav-icon"></i>
                        <p>
                            Links
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_partner')
                <li class="nav-item">
                    <a href="{{url('partners')}}" class="nav-link {{Request::segment(1) == 'partners' ? 'active' : ''}}">
                        <i class="fas fa-handshake nav-icon"></i>
                        <p>
                            Partners
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_partner')
                <li class="nav-item">
                    <a href="{{url('sliders')}}" class="nav-link {{Request::segment(1) == 'sliders' ? 'active' : ''}}">
                        <i class="fas fa-images nav-icon"></i>
                        <p>
                            Sliders
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_auto_response')
                <li class="nav-item">
                    <a href="{{url('auto_response')}}" class="nav-link {{Request::segment(1) == 'auto_response' ? 'active' : ''}}">
                        <i class="fas fa-reply nav-icon"></i>
                        <p>
                            Auto Response
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_receiver')
                <li class="nav-item">
                    <a href="{{url('receivers')}}" class="nav-link {{Request::segment(1) == 'receivers' ? 'active' : ''}}">
                        <i class="fas fa-user nav-icon"></i>
                        <p>
                            Receivers
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_receiver')
                <li class="nav-item">
                    <a href="{{url('inquiries')}}" class="nav-link {{Request::segment(1) == 'inquiries' ? 'active' : ''}}">
                        <i class="fas fa-inbox nav-icon"></i>
                        <p>
                            Inquiries
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_downloadable_categories')
                <li class="nav-item">
                    <a href="{{url('downloadable_categories')}}" class="nav-link {{Request::segment(1) == 'downloadable_categories' ? 'active' : ''}}">
                        <i class="fas fa-download nav-icon"></i>
                        <p>
                            Downloadable Categories
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_downloadables')
                <li class="nav-item">
                    <a href="{{url('downloadables')}}" class="nav-link {{Request::segment(1) == 'downloadables' ? 'active' : ''}}">
                        <i class="fas fa-download nav-icon"></i>
                        <p>
                            Downloadables
                        </p>
                    </a>
                </li>
                @endpermission

                @permission('view_monitoring')
                <li class="nav-item">
                    <a href="{{url('monitoring')}}" class="nav-link {{Request::segment(1) == 'monitoring' ? 'active' : ''}}">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>
                            Monitoring
                        </p>
                    </a>
                </li>
                @endpermission

                <li class="nav-item">
                    <a href="{{url('api-dashboard')}}" class="nav-link {{Request::segment(1) == 'api-dashboard' ? 'active' : ''}}">
                        <i class="fas fa-circle nav-icon"></i>
                        <p>
                            API Dashboard
                        </p>
                    </a>
                </li>

                @permission('view_reg_notif_receiver')
                <li class="nav-item">
                    <a href="{{url('reg_notif_receivers')}}" class="nav-link {{Request::segment(1) == 'reg_notif_receivers' ? 'active' : ''}}"><i class="fas fa-user nav-icon"></i> Reg Notification Receivers</a>
                </li>
                @endpermission

                @permission('view_data_compliance_receivers')
                <li class="nav-item">
                    <a href="{{url('data_compliance_receivers')}}" class="nav-link {{Request::segment(1) == 'data_compliance_receivers' ? 'active' : ''}}"><i class="fas fa-user nav-icon"></i> Data Compliance Receivers</a>
                </li>
                @endpermission

                @permission('view_seed_inventory_receivers')
                <li class="nav-item">
                    <a href="{{url('seed_inventory_receivers')}}" class="nav-link {{Request::segment(1) == 'seed_inventory_receivers' ? 'active' : ''}}"><i class="fas fa-user nav-icon"></i> Seed Inventory Receivers</a>
                </li>
                @endpermission

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
