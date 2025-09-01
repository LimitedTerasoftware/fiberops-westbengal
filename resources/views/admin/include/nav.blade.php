<div class="site-sidebar">
	<div class="custom-scroll custom-scroll-light">
		<ul class="sidebar-menu">
                        @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'inventory' ||  auth()->user()->role == 'super_admin' ||  auth()->user()->role == 'zone_admin')
			<li class="menu-title">@lang('admin.include.admin_dashboard')</li>
                        @endif
                        @if(auth()->user()->role == 'admin'||  auth()->user()->role == 'super_admin')
			<li>
				<a href="{{ route('admin.dashboard') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-anchor"></i></span>
					<span class="s-text">@lang('admin.include.dashboard')</span>
				</a>
			</li>
                         @endif 

                       @if(auth()->user()->role == 'inventory')
                        <li>
				<a href="{{ route('admin.inventorydashboard') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-anchor"></i></span>
					<span class="s-text">@lang('admin.include.inventory_dashboard')</span>
				</a>
			</li>

                       <li>
				<a href="{{ route('admin.viewmaps') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-anchor"></i></span>
					<span class="s-text">Maps</span>
				</a>
			</li>

                       @endif 
                      
                       @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin' ||  auth()->user()->role == 'zone_admin')

                        <li>
				<a href="{{ url('/admin/tickets') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-ticket"></i></span>
					<span class="s-text">Tickets</span>
				</a>
			</li>
                       @endif

                        @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin' ||  auth()->user()->role == 'zone_admin')

                        <li>
				<a href="{{ url('/admin/teams_status') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-ticket"></i></span>
					<span class="s-text">Teams Status</span>
				</a>
			</li>
                       @endif


                      @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin' ||  auth()->user()->role == 'zone_admin')

			<li>
				<a href="{{ route('admin.dispatcher.index') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-target"></i></span>
					<span class="s-text">@lang('admin.include.dispatcher_panel')</span>
				</a>
			</li>

                      @endif
			
                    @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin' )
			<li>
				<a href="{{ route('admin.heatmap') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-map"></i></span>
					<span class="s-text">@lang('admin.include.heat_map')</span>
				</a>
			</li>
                    @endif
		    @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin' )
			<li>
				<a href="{{ route('admin.ont-uptime') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-star"></i></span>
					<span class="s-text">@lang('admin.include.ontuptime')</span>
				</a>
			</li>
            @endif  



                    @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin' ||  auth()->user()->role == 'zone_admin')
			<li>
				<a href="{{ route('admin.reports') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-map"></i></span>
					<span class="s-text">Reports</span>
				</a>
			</li>
                    @endif 

                    @if(auth()->user()->role == 'admin'||  auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin')

			<li class="menu-title">Attendance</li>
                        <li><a class="waves-effect waves-light" href="{{ route('admin.todayattendancereport') }}"><span class="s-icon"><i class="ti-user"></i></span><span class="s-text">Today Attendance Report</span></a></li>
                     @endif        
			<!--<li><a class="waves-effect waves-light" href="{{ route('admin.attendance') }}"><span class="s-icon"><i class="ti-user"></i></span><span class="s-text">Attendance</span></a></li>-->
		     @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin')	
			<li><a class="waves-effect waves-light" href="{{ route('admin.trackattendance') }}"><span class="s-icon"><i class="ti-clipboard"></i></span><span class="s-text">Attendance Map</span></a></li>
		     @endif	
                     @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin') 
			<li><a class="waves-effect waves-light" href="{{ route('admin.reportattendance') }}"><span class="s-icon"><i class="ti-receipt"></i></span><span class="s-text">Attendance Report</span></a></li>
                     @endif
                     @if(auth()->user()->role == 'admin'||  auth()->user()->role == 'super_admin') 
			<li class="menu-title">Tracking</li>
			<li>
				<a href="{{ route('admin.map.index') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-map-alt"></i></span>
					<span class="s-text">Tracking</span>
				</a>
			</li>
                      @endif

			<!--<li><a href="{{ route('admin.tracking.provider') }}"><span class="s-icon"><i class="ti-car"></i></span><span class="s-text">Tracking Provider</span></a></li>-->
			
		      @if(auth()->user()->role == 'admin')
			<li class="menu-title">@lang('admin.include.members')</li>
                      @endif
			<!--<li>
				<a href="{{ route('admin.user.index') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-layout-grid4-alt"></i></span>
					<span class="s-text">@lang('admin.include.users')</span>
				</a>
			</li>


			<li>
				<a href="{{ route('admin.occ') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-layout-grid3-alt"></i></span>
					<span class="s-text">@lang('admin.include.occ')</span>
				</a>
			</li>-->
                        @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin')
			<li>
				<a href="{{ route('admin.provider.index') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-layout-grid2-alt"></i></span>
					<span class="s-text">@lang('admin.include.contacts')</span>
				</a>
			</li>
                        @endif

			<!--<li>
				<a href="{{ route('admin.frt') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-layout-column4-alt"></i></span>
					<span class="s-text">@lang('admin.include.frt')</span>
				</a>
			</li>

                        <li>
				<a href="{{ route('admin.zonalincharge') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-layout-column4-alt"></i></span>
					<span class="s-text">Zonal Engineers</span>
				</a>
			</li>


                        <li>
				<a href="{{ route('admin.districtincharge') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-layout-column4-alt"></i></span>
					<span class="s-text">District Engineers</span>
				</a>
			</li>-->
                        <!--<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-crown"></i></span>
					<span class="s-text">@lang('admin.include.dispatcher')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.dispatch-manager.index') }}">@lang('admin.include.list_dispatcher')</a></li>
					<li><a href="{{ route('admin.dispatch-manager.create') }}">@lang('admin.include.add_new_dispatcher')</a></li>
				</ul>
			</li>
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-crown"></i></span>
					<span class="s-text">@lang('admin.include.fleet_owner')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.fleet.index') }}">@lang('admin.include.list_fleets')</a></li>
					<li><a href="{{ route('admin.fleet.create') }}">@lang('admin.include.add_new_fleet_owner')</a></li>
				</ul>
			</li>-->
			<!--<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-crown"></i></span>
					<span class="s-text">@lang('admin.include.account_manager')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.account-manager.index') }}">@lang('admin.include.list_account_managers')</a></li>
					<li><a href="{{ route('admin.account-manager.create') }}">@lang('admin.include.add_new_account_manager')</a></li>
				</ul>
			</li>
			
			<li class="menu-title">@lang('admin.include.accounts')</li>
			<li class="with-sub">
				<a href="#" class="waves-effect waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-crown"></i></span>
					<span class="s-text">@lang('admin.include.statements')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.ride.statement') }}">@lang('admin.include.overall_ride_statments')</a></li>
					<li><a href="{{ route('admin.ride.statement.provider') }}">@lang('admin.include.provider_statement')</a></li>
					<li><a href="{{ route('admin.ride.statement.today') }}">@lang('admin.include.daily_statement')</a></li>
					<li><a href="{{ route('admin.ride.statement.monthly') }}">@lang('admin.include.monthly_statement')</a></li>
					<li><a href="{{ route('admin.ride.statement.yearly') }}">@lang('admin.include.yearly_statement')</a></li>
				</ul>
			</li>-->

			<!--<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-exchange-vertical"></i></span>
					<span class="s-text">@lang('admin.include.transaction')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.providertransfer') }}">@lang('admin.include.provider_request')</a></li>
					<li><a href="{{ route('admin.fleettransfer') }}">@lang('admin.include.fleet_request')</a></li>
					<li><a href="{{ route('admin.transactions') }}">@lang('admin.include.all_transaction')</a></li>
				</ul>
			</li> -->

			<!--<li class="menu-title">@lang('admin.include.details')</li>
			
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-view-grid"></i></span>
					<span class="s-text">@lang('admin.include.ratings') &amp; @lang('admin.include.reviews')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.user.review') }}">@lang('admin.include.user_ratings')</a></li>
					<li><a href="{{ route('admin.provider.review') }}">@lang('admin.include.provider_ratings')</a></li>
				</ul>
			</li>-->
                        @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin')
                        <li class="menu-title">Places</li>
                        @endif
                        @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin')
                        <li>
                           <a href="{{ route('admin.location.index') }}" class="waves-effect  waves-light">
	                   <span class="s-icon"><i class="ti-map-alt"></i></span>
	                   <span class="s-text">@lang('admin.include.districts')</span>
                           </a>
                       </li>
                        @endif
                        @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin')
                        <li>
                        <a href="{{ route('admin.location.block') }}" class="waves-effect  waves-light">
	                <span class="s-icon"><i class="ti-layout-menu-v"></i></span>
	                <span class="s-text">@lang('admin.include.blocks')</span>
                        </a>
                       </li>
                        @endif
                        @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'super_admin')
                         <li>
				<a href="{{ route('admin.gps.index') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-rss"></i></span>
					<span class="s-text">@lang('admin.include.gp')</span>
				</a>
			</li>
                        @endif
                       @if(auth()->user()->role == 'admin')
                        <li>
				<a href="{{ route('admin.schedulers') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-harddrives"></i></span>
					<span class="s-text">@lang('admin.include.schedules')</span>
				</a>
			</li>
                        @endif

                        @if(auth()->user()->role == 'admin')  
			<li class="menu-title">@lang('admin.include.requests')</li>
			<li>
				<a href="{{ route('admin.requests.index') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-infinite"></i></span>
					<span class="s-text">@lang('admin.include.request_history')</span>
				</a>
			</li>
			<li>
				<a href="{{ route('admin.requests.scheduled') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-palette"></i></span>
					<span class="s-text">Scheduled Tickets</span>
				</a>
			</li>
			<li class="menu-title">@lang('admin.include.general')</li>
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-view-grid"></i></span>
					<span class="s-text">@lang('admin.include.service_types')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.service.index') }}">@lang('admin.include.list_service_types')</a></li>
					<li><a href="{{ route('admin.service.create') }}">@lang('admin.include.add_new_service_type')</a></li>
				</ul>
			</li>
			<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-layout-tab"></i></span>
					<span class="s-text">@lang('admin.include.documents')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.document.index') }}">@lang('admin.include.list_documents')</a></li>
					<li><a href="{{ route('admin.document.create') }}">@lang('admin.include.add_new_document')</a></li>
				</ul>
			</li>

			<!--<li class="with-sub">
				<a href="#" class="waves-effect  waves-light">
					<span class="s-caret"><i class="fa fa-angle-down"></i></span>
					<span class="s-icon"><i class="ti-layout-tab"></i></span>
					<span class="s-text">@lang('admin.include.promocodes')</span>
				</a>
				<ul>
					<li><a href="{{ route('admin.promocode.index') }}">@lang('admin.include.list_promocodes')</a></li>
					<li><a href="{{ route('admin.promocode.create') }}">
					@lang('admin.include.add_new_promocode')</a></li>
				</ul>
			</li>-->
			
			<!--<li class="menu-title">@lang('admin.include.payment_details')</li>
			<li>
				<a href="{{ route('admin.payment') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-infinite"></i></span>
					<span class="s-text">@lang('admin.include.payment_history')</span>
				</a>
			</li>
			<li>
				<a href="{{ route('admin.settings.payment') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-money"></i></span>
					<span class="s-text">@lang('admin.include.payment_settings')</span>
				</a>
			</li>-->
			<li class="menu-title">@lang('admin.include.settings')</li>
			<li>
				<a href="{{ route('admin.settings') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-settings"></i></span>
					<span class="s-text">@lang('admin.include.site_settings')</span>
				</a>
			</li>
			
			<li class="menu-title">@lang('admin.include.others')</li>
			<li>
				<a href="{{ route('admin.cmspages') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-file"></i></span>
					<span class="s-text">@lang('admin.include.cms_pages')</span>
				</a>
			</li>
			<!--<li>
				<a href="{{ route('admin.help') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-help"></i></span>
					<span class="s-text">@lang('admin.include.help')</span>
				</a>
			</li>-->
			<li>
				<a href="{{ route('admin.push') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-smallcap"></i></span>
					<span class="s-text">@lang('admin.include.custom_push')</span>
				</a>
			</li>
			<!--<li>
				<a href="{{route('admin.translation') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-smallcap"></i></span>
					<span class="s-text">@lang('admin.include.translations')</span>
				</a>
			</li>-->
                         @endif
                         @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'inventory' ||  auth()->user()->role == 'super_admin')    
			<li class="menu-title">@lang('admin.include.account')</li>
                         @endif
                         @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'inventory')
			<li>
				<a href="{{ route('admin.profile') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-user"></i></span>
					<span class="s-text">@lang('admin.include.account_settings')</span>
				</a>
			</li>
                        @endif
                        @if(auth()->user()->role == 'admin'||  auth()->user()->role == 'inventory' ||  auth()->user()->role == 'super_admin')
			<li>
				<a href="{{ route('admin.password') }}" class="waves-effect  waves-light">
					<span class="s-icon"><i class="ti-exchange-vertical"></i></span>
					<span class="s-text">@lang('admin.include.change_password')</span>
				</a>
			</li>
                        @endif
                        @if(auth()->user()->role == 'admin' ||  auth()->user()->role == 'inventory' ||  auth()->user()->role == 'super_admin' ||  auth()->user()->role == 'zone_admin')
			<li class="compact-hide">
				<a href="{{ url('/admin/logout') }}"
                            onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
					<span class="s-icon"><i class="ti-power-off"></i></span>
					<span class="s-text">@lang('admin.include.logout')</span>
                </a>

                <form id="logout-form" action="{{ url('/admin/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
			</li>
                     @endif
			
		</ul>
	</div>
</div>