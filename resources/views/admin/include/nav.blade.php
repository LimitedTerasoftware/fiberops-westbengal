{{-- Desktop Toggle Button --}}
@php
    $user = Session::get('user');
    $stateName = 'westbengal'; 
    if ($user && isset($user->state_id)) {
        $stateName = $user->state_id == 1 ? 'westbengal' : 'andaman';
    }
@endphp
<div class="terrasoft-desktop-toggle">
    <button class="terrasoft-toggle-btn" id="desktopToggle" aria-label="Toggle sidebar">
        <i class="fa fa-bars"></i>
    </button>
</div>

<div class="terrasoft-mobile-header">
    <div class="terrasoft-mobile-brand">
        <img src="{{ Setting::get('site_logo', asset('logo-black.png')) }}" alt="Fiber ops" class="terrasoft-mobile-logo">
        <span class="terrasoft-mobile-title">Fiber ops</span>
    </div>
    <button class="terrasoft-hamburger" id="mobileMenuToggle" aria-label="Toggle menu">
        <span class="terrasoft-hamburger-line"></span>
        <span class="terrasoft-hamburger-line"></span>
        <span class="terrasoft-hamburger-line"></span>
    </button>
</div>

<div class="terrasoft-navigation" id="navigationSidebar">
    <div class="terrasoft-nav-scroll">
        {{-- Desktop Logo --}}
        <div class="terrasoft-nav-brand">
            <img src="{{ Setting::get('site_logo', asset('logo-black.png')) }}" alt="Fiber ops" class="terrasoft-nav-logo">
            <span class="terrasoft-nav-title">Fiber ops</span>
        </div>

        <ul class="terrasoft-nav-menu">
            {{-- 1. Executive Dashboard --}}
            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'inventory' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin' || auth()->user()->role=='district_incharge'
                || auth()->user()->role=='client')
            <li class="terrasoft-nav-group">
                <div class="terrasoft-nav-header" data-submenu="dashboard">
                    <i class="ti-bar-chart"></i>
                    <span>Dashboard</span>
                    <i class="fa fa-angle-down terrasoft-nav-arrow"></i>
                </div>
                <ul class="terrasoft-nav-submenu" id="submenu-dashboard">
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin' || auth()->user()->role=='district_incharge' || auth()->user()->role=='client')
                    <li><a href="{{ route('admin.dashboard') }}" ><i class="ti-bar-chart"></i> Tickets Overview</a></li>
                    <li><a href="{{ route('admin.workforce') }}" ><i class="ti-bar-chart"></i> Workforce Overview</a></li>

                    @endif
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin' ||  auth()->user()->role == 'zone_admin' )

                    <li><a href="{{ route('admin.uptime') }}" ><i class="ti-stats-up"></i> GP Trends</a></li>
                    @endif
                    @if(auth()->user()->role == 'inventory')
                    <li><a href="{{ route('admin.inventorydashboard') }}" ><i class="ti-anchor"></i> @lang('admin.include.inventory_dashboard')</a></li>
                    <li><a href="{{ route('admin.viewmaps') }}" ><i class="ti-pie-chart"></i> Maps</a></li>
                    @endif
                </ul>
            </li>
            <li class="terrasoft-nav-group"> 
                <div class="terrasoft-nav-header text-info" id="MapView"> 
                    <a href="{{ route('admin.heatmap') }}" ><i class="ti-map-alt text-white"></i> <span class="text-white"> &nbsp; Map View</span></a> 
                </div>
            </li>
            @endif

            {{-- 2. Tickets & Dispatch --}}
            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin' || auth()->user()->role=='district_incharge' || auth()->user()->role=='client')
            <li class="terrasoft-nav-group">
                <div class="terrasoft-nav-header" data-submenu="tickets">
                    <i class="ti-ticket"></i>
                    <span>Tickets</span>
                    <i class="fa fa-angle-down terrasoft-nav-arrow"></i>
                </div>
                <ul class="terrasoft-nav-submenu" id="submenu-tickets">
                    <li><a href="{{ url('/admin/tickets') }}" ><i class="ti-ticket"></i> Tickets</a></li>
                    <li><a href="{{ route('admin.dailyrepots') }}"><i class="ti-check"></i> Daily Report</a></li>
                    <li><a href="{{ route('admin.patrollertickets') }}"><i class="ti-check"></i> Patroller Tickets</a></li>

                </ul>
            </li>
            @endif

            {{-- 3. Team Monitoring --}}
            <li class="terrasoft-nav-group">
                <div class="terrasoft-nav-header" data-submenu="team">
                    <i class="ti-user"></i>
                    <span>Team </span>
                    <i class="fa fa-angle-down terrasoft-nav-arrow"></i>
                </div>
                <ul class="terrasoft-nav-submenu" id="submenu-team">
                    <li><a href="{{ url('/admin/teams_status') }}" ><i class="ti-user"></i>FRT Teams Status</a></li>
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin' || auth()->user()->role=='district_incharge')
                    <li><a href="{{ route('admin.provider.index') }}" ><i class="ti-id-badge"></i> Technicians List</a></li>
                    @endif
                </ul>
            </li>

            {{-- 4. Attendance --}}
            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin' || auth()->user()->role=='district_incharge' || auth()->user()->role=='client')

            <li class="terrasoft-nav-group">
                <div class="terrasoft-nav-header" data-submenu="attendance">
                    <i class="ti-clipboard"></i>
                    <span>Attendance</span>
                    <i class="fa fa-angle-down terrasoft-nav-arrow"></i>
                </div>
                <ul class="terrasoft-nav-submenu" id="submenu-attendance">

                    <li><a href="{{ route('admin.attendance_dashboard') }}" ><i class="ti-clipboard"></i> Attendance Dashboard</a></li>
                    <li><a href="{{ route('admin.attendance_list') }}"><i class="ti-list"></i> Attendance List</a></li>
                    <li><a href="{{ route('admin.todayattendancereport') }}" ><i class="ti-check-box"></i> Today's Attendance</a></li>
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin')

                    <li><a href="{{ route('admin.trackattendance') }}" ><i class="ti-map"></i> Attendance Map View</a></li>
                    <li><a href="{{ route('admin.reportattendance') }}" ><i class="ti-receipt"></i> Attendance Reports</a></li>
                    @endif
                </ul>
            </li>
            @endif

             {{-- 5. Inventory --}}
            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin')

            <li class="terrasoft-nav-group">
                <div class="terrasoft-nav-header" data-submenu="inventory">
                    <i class="ti-layers-alt"></i>
                    <span>Store Inventory</span>
                    <i class="fa fa-angle-down terrasoft-nav-arrow"></i>
                </div>
                <ul class="terrasoft-nav-submenu" id="submenu-inventory">

                    <!-- <li><a href="{{ route('admin.stock-entry.index') }}" ><i class="ti-list"></i>Stock Entry</a></li>
                    <li><a href="{{ route('admin.stock-issue.index') }}"><i class="ti-list"></i>Stock Issue</a></li> -->
                    <li><a href="{{ route('admin.stock-report') }}"><i class="ti-list"></i>Stock Report</a></li>
                    <li><a href="{{ route('admin.materials.index') }}" ><i class="ti-package"></i>Materials</a></li>


                </ul>
            </li>
            @endif

             @if(auth()->user()->role == 'admin' || auth()->user()->role == 'zone_admin' )
            {{-- 5. Tracking & Movement --}}
            <li class="terrasoft-nav-group">
                <div class="terrasoft-nav-header" data-submenu="enclosure">
                    <i class="fa-solid fa-code-fork"></i>
                    <span>Joint Enclosure</span>
                    <i class="fa fa-angle-down terrasoft-nav-arrow"></i>
                </div>
                <ul class="terrasoft-nav-submenu" id="submenu-enclosure">
                    <li><a href="{{ route('admin.joint_enclouser_reports') }}" ><i class="ti-map-alt"></i>Joint Enclosure report</a></li>
                    <li><a href="{{ route('admin.joint_enclosure_download') }}" ><i class="ti-map-alt"></i>Joint Enclosure Download</a></li>
                </ul>
            </li>
              @endif

          
            @if(auth()->user()->role == 'admin')
            {{-- 5. Tracking & Movement --}}
            <li class="terrasoft-nav-group">
                <div class="terrasoft-nav-header" data-submenu="tracking">
                    <i class="ti-location-pin"></i>
                    <span>Tracking</span>
                    <i class="fa fa-angle-down terrasoft-nav-arrow"></i>
                </div>
                <ul class="terrasoft-nav-submenu" id="submenu-tracking">
                    <li><a href="{{ route('admin.map.index') }}" ><i class="ti-map-alt"></i> Live Tracking</a></li>
                </ul>
            </li>
              @endif
          

            {{-- 6. Places --}}
            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin')
            <li class="terrasoft-nav-group">
                <div class="terrasoft-nav-header" data-submenu="places">
                    <i class="fa fa-globe"></i>
                    <span>Places</span>
                    <i class="fa fa-angle-down terrasoft-nav-arrow"></i>
                </div>
                <ul class="terrasoft-nav-submenu" id="submenu-places">
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin')

                    <li><a href="{{ route('admin.location.index') }}" ><i class="ti-map-alt"></i> @lang('admin.include.districts')</a></li>
                    <li><a href="{{ route('admin.location.block') }}" ><i class="ti-layout-menu-v"></i> @lang('admin.include.blocks')</a></li>
                    @endif
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin')

                    <li><a href="{{ route('admin.gps.index') }}" ><i class="ti-layout-grid2"></i> GP List</a></li>
                    @endif
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'super_admin')

                     <li><a href="{{ route('admin.olt-locations.index') }}" ><i class="ti-layout-grid2"></i> OLT</a></li>
                    @endif

                </ul>
            </li>
            @endif

            {{-- 7. Others --}}
            @if(auth()->user()->role == 'admin')
            <li class="terrasoft-nav-group">
                <div class="terrasoft-nav-header" data-submenu="others">
                    <i class="ti-view-grid"></i>
                    <span>Others</span>
                    <i class="fa fa-angle-down terrasoft-nav-arrow"></i>
                </div>
                <ul class="terrasoft-nav-submenu" id="submenu-others">
                    <li><a href="{{ route('admin.service.index') }}" ><i class="ti-folder"></i> @lang('admin.include.list_service_types')</a></li>
                    <li><a href="{{ route('admin.service.create') }}" ><i class="ti-image"></i> @lang('admin.include.add_new_service_type')</a></li>
                    <li><a href="{{ route('admin.document.index') }}" ><i class="ti-agenda"></i> @lang('admin.include.list_documents')</a></li>
                    <li><a href="{{ route('admin.document.create') }}"><i class="ti-layout-tab"></i> @lang('admin.include.add_new_document')</a></li>
                    <li><a href="{{ route('admin.cmspages') }}" ><i class="ti-file"></i> @lang('admin.include.cms_pages')</a></li>
                    <li><a href="{{ route('admin.push') }}" ><i class="ti-smallcap"></i> @lang('admin.include.custom_push')</a></li>
                </ul>
            </li>
            @endif

            {{-- 8. Account / Settings --}}
            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'inventory' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin' || auth()->user()->role=='district_incharge' || auth()->user()->role=='client')
            <li class="terrasoft-nav-group">
                <div class="terrasoft-nav-header" data-submenu="account">
                    <i class="ti-settings"></i>
                    <span>Account</span>
                    <i class="fa fa-angle-down terrasoft-nav-arrow"></i>
                </div>
                <ul class="terrasoft-nav-submenu" id="submenu-account">
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'inventory')
                    <li><a href="{{ route('admin.profile') }}" ><i class="ti-user"></i> Profile</a></li>
                    @endif
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'inventory' || auth()->user()->role == 'super_admin')
                    <li><a href="{{ route('admin.password') }}"><i class="ti-key"></i> Change Password</a></li>
                    <li><a href="{{ route('admin.settings') }}" ><i class="ti-settings"></i> Site Settings</a></li>
                    @endif
                   @if(auth()->user()->role == 'admin' || auth()->user()->role == 'inventory' || auth()->user()->role == 'super_admin' || auth()->user()->role == 'zone_admin' || auth()->user()->role=='district_incharge' || auth()->user()->role=='client')

                    <li><a href="{{ url('/admin/logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="ti-power-off"></i> Logout
                    </a></li>
                    <form id="logout-form" action="{{ url('/admin/logout') }}" method="POST" style="display:none;">{{ csrf_field() }}</form>
                     @endif
                </ul>
            </li>
         
            @endif
            @if(auth()->user()->role !='client')
            {{-- AI Assistant --}}
            <li class="terrasoft-ai-assistant">
                <a href="#" id="openChatbot">
                    <i class="ti-comments"></i>
                    <span>AI Assistant</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>

{{-- Mobile Overlay --}}
<div class="terrasoft-mobile-overlay" id="mobileOverlay"></div>

<!-- AI Chatbot Modal -->
<div id="chatbotModal" class="chatbot-modal">
  <div class="chatbot-container">
    <div class="chatbot-header">
      <h3><i class="ti-comments"></i> AI Assistant</h3>
      <button id="closeChatbot" class="close-btn">&times;</button>
    </div>
    
    <div class="chatbot-messages" id="chatMessages">
      <div class="message bot-message">
        <div class="message-content">
          <i class="ti-robot"></i>
          <span>Hello! I'm your AI assistant. Ask me anything about your data, reports, or analytics.</span>
        </div>
      </div>
    </div>
    
    <div class="chatbot-input">
      <div class="input-group">
        <input type="text" id="chatInput" placeholder="Ask me anything..." maxlength="500">
        <button id="sendMessage" disabled>
          <i class="ti-arrow-right"></i>
        </button>
      </div>
      <div class="input-counter">
        <span id="charCounter">0/500</span>
      </div>
    </div>
    
    <div class="chatbot-loading" id="loadingIndicator" style="display: none;">
      <div class="loading-dots">
        <span></span>
        <span></span>
        <span></span>
      </div>
      <span>AI is thinking...</span>
    </div>
  </div>
</div>

{{-- CSS Styles --}}
<link rel="stylesheet" href="{{ asset('/css/AIchat.css')}}">
<link rel="stylesheet" href="{{ asset('/css/SideNav.css')}}">

<script>
let stateName = {!! json_encode($stateName) !!};

document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const desktopToggle = document.getElementById('desktopToggle');
    const navigationSidebar = document.getElementById('navigationSidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const navHeaders = document.querySelectorAll('.terrasoft-nav-header');
    const body = document.body;

    // Mobile menu toggle
    mobileMenuToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        navigationSidebar.classList.toggle('active');
        mobileOverlay.classList.toggle('active');
        document.body.style.overflow = navigationSidebar.classList.contains('active') ? 'hidden' : '';
    });

    // Desktop toggle
    desktopToggle.addEventListener('click', function() {
        navigationSidebar.classList.toggle('terrasoft-collapsed');
        body.classList.toggle('terrasoft-sidebar-collapsed');
        
        // Change icon
        const icon = this.querySelector('i');
        if (navigationSidebar.classList.contains('terrasoft-collapsed')) {
            icon.className = 'fa fa-chevron-right';
        } else {
            icon.className = 'fa fa-bars';
        }
    });

    // Close mobile menu when overlay is clicked
    mobileOverlay.addEventListener('click', function() {
        mobileMenuToggle.classList.remove('active');
        navigationSidebar.classList.remove('active');
        this.classList.remove('active');
        document.body.style.overflow = '';
    });

    // Submenu toggle
    navHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const submenuId = 'submenu-' + this.dataset.submenu;
            const submenu = document.getElementById(submenuId);
            const arrow = this.querySelector('.terrasoft-nav-arrow');
            
            if (submenu) {
                submenu.classList.toggle('active');
                arrow.classList.toggle('active');
            }
        });
    });

    // Auto-expand parent menu if child is active
    const activeLinks = document.querySelectorAll('.terrasoft-active');
    activeLinks.forEach(link => {
        const parentSubmenu = link.closest('.terrasoft-nav-submenu');
        if (parentSubmenu) {
            const parentHeader = parentSubmenu.previousElementSibling;
            const arrow = parentHeader.querySelector('.terrasoft-nav-arrow');
            parentSubmenu.classList.add('active');
            arrow.classList.add('active');
        }
    });

    // Close mobile menu when window is resized to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            mobileMenuToggle.classList.remove('active');
            navigationSidebar.classList.remove('active');
            mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
    
const currentUrl = window.location.pathname;

const mapLink = document.querySelector('#MapView a');
const href = new URL(mapLink.href).pathname;

if (mapLink && href === currentUrl) {
    mapLink.classList.add('terrasoft-active');
    mapLink.closest('.terrasoft-nav-header').classList.add('active');
}

document.querySelectorAll('.terrasoft-nav-submenu a').forEach(link => {
    const linkPath = new URL(link.href).pathname;

    if (linkPath === currentUrl) {
        link.classList.add('terrasoft-active');

        let parentUl = link.closest('.terrasoft-nav-submenu');
        while (parentUl) {
            parentUl.classList.add('active');
            const prevTitle = parentUl.previousElementSibling;
            if (prevTitle && prevTitle.classList.contains('terrasoft-nav-header')) {
                prevTitle.classList.add('active');
            }
            parentUl = parentUl.closest('.terrasoft-nav-submenu')?.closest('li')?.closest('.terrasoft-nav-submenu') || null;
        }
    }
});

});
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('chatbotModal');
    const openBtn = document.getElementById('openChatbot');
    const closeBtn = document.getElementById('closeChatbot');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendMessage');
    const messagesContainer = document.getElementById('chatMessages');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const charCounter = document.getElementById('charCounter');

    // Open chatbot
    openBtn.addEventListener('click', function(e) {
        e.preventDefault();
        modal.style.display = 'block';
        chatInput.focus();
    });

    // Close chatbot
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Close on outside click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Character counter
    chatInput.addEventListener('input', function() {
        const length = this.value.length;
        charCounter.textContent = `${length}/500`;
        sendBtn.disabled = length === 0;
        
        if (length > 450) {
            charCounter.style.color = '#ff5722';
        } else {
            charCounter.style.color = '#666';
        }
    });

    // Send message on Enter
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !sendBtn.disabled) {
            sendMessage();
        }
    });

    // Send message on button click
    sendBtn.addEventListener('click', sendMessage);

    function sendMessage() {
        const question = chatInput.value.trim();
        if (!question) return;
        const finalQuestion = question + ' (' + stateName + ')';

        // Add user message
        addMessage(question, 'user');
        
        // Clear input
        chatInput.value = '';
        charCounter.textContent = '0/500';
        sendBtn.disabled = true;
        
        // Show loading
        showLoading(true);
        
       
        fetch('https://westbengal.fyndo.ai/agent/ask', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ question: finalQuestion })
        })
        .then(response => response.json())
        .then(data => {
            showLoading(false);
            if (data.success) {
                addBotResponse(data);
            } else {
                addMessage('Sorry, I encountered an error processing your request. Please try again.', 'bot');
            }
        })
        .catch(error => {
            showLoading(false);
            console.error('Error:', error);
            addMessage('Sorry, I\'m having trouble connecting right now. Please check your connection and try again.', 'bot');
        });
    }

    function addMessage(content, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}-message`;
        
        const icon = type === 'user' ? 'ti-user' : 'ti-robot';
        
        messageDiv.innerHTML = `
            <div class="message-content">
                <i class="${icon}"></i>
                <span>${content}</span>
            </div>
        `;
        
        messagesContainer.appendChild(messageDiv);
        scrollToBottom();
    }

    function addBotResponse(data) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot-message';
        
        let responseHTML = `
            <div class="message-content">
                <i class="ti-robot"></i>
                <div>
        `;
        
        // Add data table if available
        // if (data.data && data.data.length > 0) {
        //     responseHTML += `
        //         <div class="response-section">
        //             <h4>📊 Query Results:</h4>
        //             <table class="data-table">
        //                 <thead>
        //                     <tr>
        //     `;
            
        //     // Generate table headers
        //     const headers = Object.keys(data.data[0]);
        //     headers.forEach(header => {
        //         responseHTML += `<th>${header.replace(/_/g, ' ').toUpperCase()}</th>`;
        //     });
            
        //     responseHTML += `
        //                     </tr>
        //                 </thead>
        //                 <tbody>
        //     `;
            
        //     // Generate table rows
        //     data.data.forEach(row => {
        //         responseHTML += '<tr>';
        //         headers.forEach(header => {
        //             let value = row[header];
        //             // Truncate long text
        //             if (typeof value === 'string' && value.length > 50) {
        //                 value = value.substring(0, 50) + '...';
        //             }
        //             responseHTML += `<td>${value || 'N/A'}</td>`;
        //         });
        //         responseHTML += '</tr>';
        //     });
            
        //     responseHTML += `
        //                 </tbody>
        //             </table>
        //         </div>
        //     `;
        // }
        
        // Add chart if available
        if (data.chartBase64) {
            responseHTML += `
                <div class="response-section">
                    <h4>Visual Analysis:</h4>
                    <div class="chart-container">
                        <img src="${data.chartBase64}" alt="Data Visualization" />
                    </div>
                </div>
            `;
        }
        
        // Add summary
        if (data.summary) {
            responseHTML += `
                <div class="response-section">
                    <h4> AI Summary:</h4>
                    <div class="summary-text">${data.summary}</div>
                </div>
            `;
        }
        
        // if (data.sql) {
        //     responseHTML += `
        //         <div class="response-section">
        //             <h4>Generated Query:</h4>
        //             <code style="background: #f5f5f5; padding: 10px; border-radius: 4px; display: block; font-size: 11px; overflow-x: auto;">${data.sql}</code>
        //         </div>
        //     `;
        // }
        
        responseHTML += `
                </div>
            </div>
        `;
        
        messageDiv.innerHTML = responseHTML;
        messagesContainer.appendChild(messageDiv);
        scrollToBottom();
    }

    function showLoading(show) {
        loadingIndicator.style.display = show ? 'flex' : 'none';
        if (show) {
            scrollToBottom();
        }
    }

    function scrollToBottom() {
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 100);
    }
});

</script>