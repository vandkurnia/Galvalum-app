  <!-- Topbar -->
  <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

      <!-- Sidebar Toggle (Topbar) -->
      <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
          <i class="fa fa-bars"></i>
      </button>

      <!-- Topbar Navbar -->
      <ul class="navbar-nav ml-auto">

          <!-- Nav Item - Search Dropdown (Visible Only XS) -->
          <li class="nav-item dropdown no-arrow d-sm-none">
              <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-search fa-fw"></i>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                  aria-labelledby="searchDropdown">
                  <form class="form-inline mr-auto w-100 navbar-search">
                      <div class="input-group">
                          <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                              aria-label="Search" aria-describedby="basic-addon2">
                          <div class="input-group-append">
                              <button class="btn btn-primary" type="button">
                                  <i class="fas fa-search fa-sm"></i>
                              </button>
                          </div>
                      </div>
                  </form>
              </div>
          </li>


          <style>
              .is_read {
                  background-color: #efefef;
              }
          </style>
          <!-- Nav Item - Alerts -->
          <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-bell fa-fw"></i>
                  <!-- Counter - Alerts -->
                  <span class="badge badge-danger badge-counter" id="notification-counter">0</span>
              </a>
              <!-- Dropdown - Alerts -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                  aria-labelledby="alertsDropdown">
                  <h6 class="dropdown-header">
                      Notifikasi Center
                  </h6>
                  <div class="dropdown-list-modal">
                      <a class="dropdown-item d-flex align-items-center" href="#">
                          <div class="mr-3">
                              <div class="icon-circle bg-warning">
                                  <i class="fas fa-exclamation-triangle text-white"></i>
                              </div>
                          </div>
                          <div>
                              <div class="small text-gray-500">December 2, 2019</div>
                              Spending Alert: We've noticed unusually high spending for your account.
                          </div>
                      </a>
                  </div>
                  {{-- <a class="dropdown-item d-flex align-items-center" href="#">
                      <div class="mr-3">
                          <div class="icon-circle bg-primary">
                              <i class="fas fa-file-alt text-white"></i>
                          </div>
                      </div>
                      <div>
                          <div class="small text-gray-500">December 12, 2019</div>
                          <span class="font-weight-bold">A new monthly report is ready to download!</span>
                      </div>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="#">
                      <div class="mr-3">
                          <div class="icon-circle bg-success">
                              <i class="fas fa-donate text-white"></i>
                          </div>
                      </div>
                      <div>
                          <div class="small text-gray-500">December 7, 2019</div>
                          $290.29 has been deposited into your account!
                      </div>
                  </a> --}}

                  {{-- <a class="dropdown-item text-center small text-gray-500" href="#">Show All
                      Alerts</a> --}}
              </div>
          </li>

          <script>
              $(document).ready(function() {

                  function ambilNotifikasi() {
                      $.ajax({
                          url: '/notifications',
                          method: 'GET',
                          success: function(data) {
                              var notifications = data;
                              var notificationsList = '';
                              let totalnotification = 0;

                              const notificationCounter = document.getElementById('notification-counter');
                              if (notifications.length === 0) {

                                  notificationCounter.innerText = totalnotification;
                                  notificationsList =
                                      '<a class="dropdown-item d-flex align-items-center" href="#"><div class="mr-3"><div class="icon-circle bg-secondary"><i class="fas fa-info text-white"></i></div></div><div><div class="small text-gray-500">No new notifications</div></div></a>';
                              } else {


                                  $.each(notifications, function(index, notification) {
                                      // Check if read_at is empty and increment the counter
                                      if (!notification.read_at) {
                                          totalnotification++;
                                      }
                                      var notification_is_read = notification.read_at ? 'is_read' :
                                          '';

                                      notificationsList +=
                                          '<div class="dropdown-item d-flex align-items-center ' +
                                          notification_is_read + '" data-id="' + notification
                                          .id_notifikasi + '" data-url="' + notification.url + '">';
                                      notificationsList += '<div class="mr-3">';
                                      notificationsList += '<div class="icon-circle bg-warning">';
                                      notificationsList += '<i class="' + notification.icon +
                                          '"></i>';
                                      notificationsList += '</div>';
                                      notificationsList += '</div>';
                                      notificationsList += '<div>';
                                      notificationsList += '<div class="small text-gray-500">' +
                                          new Date(notification.created_at).toLocaleDateString() +
                                          '</div>';
                                      notificationsList += notification.message;
                                      notificationsList += '</div>';
                                      notificationsList +=
                                          '<button class="btn btn-sm btn-danger ml-auto delete-notification"><i class="fas fa-trash-alt"></i></button>';
                                      notificationsList += '</div>';
                                  });

                                  notificationCounter.innerText = totalnotification;
                              }
                              $('.dropdown-list-modal').html(notificationsList);
                          }
                      });
                  }
                  // Call ambilNotifikasi when the document is ready
                  ambilNotifikasi();

                  // Call ambilNotifikasi when the alertsDropdown is clicked
                  $('#alertsDropdown').on('click', function() {
                      ambilNotifikasi();
                  });



                  // Handle the click on the notification delete button
                  $('.dropdown-list-modal').on('click', '.delete-notification', function(e) {
                      e.stopPropagation(); // Prevent dropdown from closing

                      var notificationId = $(this).closest('.dropdown-item').data('id');
                      var notificationUrl = $(this).closest('.dropdown-item').data('url');

                      // Send AJAX request to delete the notification
                      $.ajax({
                          url: '/notifications/' + notificationId + '/delete',
                          method: 'POST',
                          data: {
                              _token: '{{ csrf_token() }}' // Add CSRF token if needed
                          },
                          success: function(response) {
                              alert(JSON.stringify(response));
                              // Remove the deleted notification from the list
                              $(this).closest('.dropdown-item').remove();
                              // Optionally, redirect to the notification URL after deletion
                              window.location.href = notificationUrl;
                          }.bind(this)
                      });
                  });


                  // Handle the click on the notification items
                  $('.dropdown-list-modal').on('click', '.dropdown-item', function(e) {
                      e.preventDefault();
                      var notificationUrl = $(this).data('url');

                      $.ajax({
                          url: notificationUrl,
                          method: 'GET',
                          success: function(response) {
                              // Add class to mark as read
                              $(this).addClass('is_read');
                              // Redirect to the URL
                              window.location.href = notificationUrl;
                          }.bind(this)
                      });
                  });
              });
          </script>



          <div class="topbar-divider d-none d-sm-block"></div>

          <!-- Nav Item - User Information -->
          <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->nama_admin }}</span>
                  <img class="img-profile rounded-circle" src="{{ secure_asset('assets/img/undraw_profile.svg') }} ">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                  <a class="dropdown-item" href="#">
                      <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                      Settings
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                      <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                      Logout
                  </a>
              </div>
          </li>

      </ul>

  </nav>
