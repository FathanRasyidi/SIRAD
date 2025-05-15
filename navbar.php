<aside
  class="fixed-sidebar flex flex-col w-64 h-full pb-6 px-5 py-8 overflow-y-auto bg-white border-r rtl:border-r-0 rtl:border-l">
  <a class="navbar-brand text-gray-600 flex items-center pl-2 my-2">
    <img src="img/sr.png" alt="" width="50" height="50" class="d-inline-block" id="logo" style="margin-right: 10px">
    <span class="ml-2">Sistem Informasi Radiologi</span>
  </a>

  <div class="flex flex-col justify-between flex-1 mt-6">
    <nav class="-mx-3 space-y-6 ">
      <div class="space-y-3 ">
        <label class="px-3 text-xs text-gray-500 uppercase ">navigasi</label>

        <a class="flex items-center px-3 py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-gray-300' : ''; ?> text-gray-600 transition-colors duration-300 transform rounded-lg hover:bg-gray-200 hover:text-gray-700"
          href="index.php">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
          </svg>

          <span class="mx-2 text-sm font-medium">Dashboard</span>
        </a>

        <a class="flex items-center px-3 py-2 <?php echo in_array(basename($_SERVER['PHP_SELF']), ['pasien.php', 'edit_pasien.php']) ? 'bg-gray-300' : ''; ?> text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
          href="pasien.php">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
          </svg>

          <span class="mx-2 text-sm font-medium">Data Pasien</span>
        </a>
        <?php if ($user_type == "admin") { ?>
          <a class="flex items-center px-3 py-2 <?php echo in_array(basename($_SERVER['PHP_SELF']), ['user.php', 'edit_user.php']) ? 'bg-gray-300' : ''; ?> text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
            href="user.php">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"
              class="h-5 w-5">
              <path fill-rule="evenodd"
                d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
                clip-rule="evenodd"></path>
            </svg>

            <span class="mx-2 text-sm font-medium">Akun User</span>
          </a>
        <?php } ?>
        <?php if ($user_type != 'pasien') { ?>
          <a class="flex items-center px-3 py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'akun_pasien.php' ? 'bg-gray-300' : ''; ?> text-gray-600 transition-colors duration-300 transform rounded-lg  hover:bg-gray-200 hover:text-gray-700"
            href="akun_pasien.php">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-6 h-6 group-hover:text-indigo-400">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
            <span class="mx-2 text-sm font-medium">Akun Pasien</span>
          </a>
        <?php } ?>
      </div>
    </nav>
    <a class="flex align-bottom items-center px-3 mt-20 py-2 text-red-600 transition-colors duration-300 transform rounded-lg hover:bg-gray-200 hover:text-red-600"
      href="logout.php">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"
        class="h-5 w-5">
        <path fill-rule="evenodd"
          d="M12 2.25a.75.75 0 01.75.75v9a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM6.166 5.106a.75.75 0 010 1.06 8.25 8.25 0 1011.668 0 .75.75 0 111.06-1.06c3.808 3.807 3.808 9.98 0 13.788-3.807 3.808-9.98 3.808-13.788 0-3.808-3.807-3.808-9.98 0-13.788a.75.75 0 011.06 0z"
          clip-rule="evenodd"></path>
      </svg>
      <span class="mx-2 text-sm font-medium">Logout</span>
    </a>
  </div>
</aside>