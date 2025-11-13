<?php
include 'db_connect.php';
$result = $conn->query("SELECT * FROM appointments ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Appointment Requests - DentLink</title>

<!-- DataTables & SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
  :root {
    --primary-color: #80A1BA;
    --secondary-color: #91C4C3;
    --accent-color: #B4DEBD;
    --light-color: #FFF7DD;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #B4DEBD 0%, #FFF7DD 100%);
    min-height: 100vh;
    padding: 30px;
  }

  .header-section {
    background: white;
    padding: 30px 40px;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(128, 161, 186, 0.15);
    margin-bottom: 30px;
    border-top: 5px solid var(--primary-color);
  }

  .header-section h2 {
    color: var(--primary-color);
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
  }

  .header-section p {
    color: #666;
    font-size: 1rem;
  }

  .table-container {
    background: white;
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(128, 161, 186, 0.15);
    border-left: 6px solid var(--secondary-color);
  }

  /* DataTables Customization */
  .dataTables_wrapper {
    padding: 0;
  }

  .dataTables_filter input {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 8px 15px;
    margin-left: 10px;
    transition: all 0.3s ease;
  }

  .dataTables_filter input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(128, 161, 186, 0.1);
  }

  .dataTables_length select {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 8px 15px;
    margin: 0 10px;
    transition: all 0.3s ease;
  }

  .dataTables_length select:focus {
    outline: none;
    border-color: var(--primary-color);
  }

  table.dataTable {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
  }

  table.dataTable thead th {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    padding: 18px 15px;
    text-align: center;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
  }

  table.dataTable tbody td {
    padding: 15px;
    text-align: center;
    border-bottom: 1px solid #f0f0f0;
    color: #333;
    font-size: 0.9rem;
  }

  table.dataTable tbody tr {
    transition: all 0.3s ease;
  }

  table.dataTable tbody tr:hover {
    background-color: rgba(180, 222, 189, 0.1);
    transform: scale(1.01);
  }

  /* Status Badges */
  .status-pending {
    background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-block;
  }

  .status-approved {
    background: linear-gradient(135deg, var(--accent-color) 0%, var(--secondary-color) 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-block;
  }

  .status-denied {
    background: linear-gradient(135deg, #ef5350 0%, #e53935 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-block;
  }

  /* Buttons */
  button {
    padding: 8px 16px;
    border: none;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    margin: 3px;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .approve {
    background: linear-gradient(135deg, var(--accent-color) 0%, #91C4C3 100%);
  }

  .approve:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(180, 222, 189, 0.4);
  }

  .deny {
    background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
  }

  .deny:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(244, 67, 54, 0.4);
  }

  /* Pagination */
  .dataTables_paginate .paginate_button {
    padding: 8px 12px;
    margin: 0 3px;
    border-radius: 8px;
    border: 2px solid var(--primary-color) !important;
    background: white !important;
    color: var(--primary-color) !important;
    transition: all 0.3s ease;
  }

  .dataTables_paginate .paginate_button:hover {
    background: var(--primary-color) !important;
    color: white !important;
    transform: translateY(-2px);
  }

  .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
    color: white !important;
    border: none !important;
  }

  /* Info Text */
  .dataTables_info {
    color: #666;
    font-size: 0.9rem;
    padding-top: 15px;
  }

  /* Back Button */
  .back-btn {
    background: white;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
    padding: 12px 25px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    margin-bottom: 20px;
    transition: all 0.3s ease;
  }

  .back-btn:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
  }

  /* Responsive */
  @media (max-width: 768px) {
    body {
      padding: 15px;
    }

    .header-section {
      padding: 20px;
    }

    .header-section h2 {
      font-size: 1.5rem;
    }

    .table-container {
      padding: 20px;
      overflow-x: auto;
    }

    table.dataTable {
      font-size: 0.85rem;
    }

    table.dataTable thead th,
    table.dataTable tbody td {
      padding: 10px 8px;
    }

    button {
      padding: 6px 12px;
      font-size: 0.8rem;
    }
  }
</style>
</head>
<body>

<a href="admin_dashboard.php" class="back-btn">
  <i class="bi bi-arrow-left"></i> Back to Dashboard
</a>

<div class="header-section">
  <h2><i class="bi bi-calendar-check"></i> Appointment Requests Management</h2>
  <p>Review and manage all patient appointment requests</p>
</div>

<div class="table-container">
  <table id="appointmentsTable">
    <thead>
      <tr>
        <th><i class="bi bi-person"></i> Name</th>
        <th><i class="bi bi-envelope"></i> Email</th>
        <th><i class="bi bi-calendar-date"></i> Date</th>
        <th><i class="bi bi-clock"></i> Time</th>
        <th><i class="bi bi-geo-alt"></i> Location</th>
        <th><i class="bi bi-clipboard-pulse"></i> Service</th>
        <th><i class="bi bi-info-circle"></i> Status</th>
        <th><i class="bi bi-gear"></i> Action</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()) { ?>
      <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= date('M d, Y', strtotime($row['date'])) ?></td>
        <td><?= date('h:i A', strtotime($row['start_time'])) ?></td>
        <td><?= htmlspecialchars($row['location']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td>
          <?php 
            $status = $row['status'];
            if ($status == 'pending') {
              echo '<span class="status-pending">PENDING</span>';
            } elseif ($status == 'approved') {
              echo '<span class="status-approved">APPROVED</span>';
            } else {
              echo '<span class="status-denied">DENIED</span>';
            }
          ?>
        </td>
        <td>
          <?php if ($row['status'] == 'pending') { ?>
            <button class="approve" data-id="<?= $row['id'] ?>">
              <i class="bi bi-check-circle"></i> Approve
            </button>
            <button class="deny" data-id="<?= $row['id'] ?>">
              <i class="bi bi-x-circle"></i> Deny
            </button>
          <?php } else { 
            echo '<span style="color: #999; font-weight: 600;">' . strtoupper($row['status']) . '</span>'; 
          } ?>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>

<!-- jQuery, DataTables, and SweetAlert2 JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
  // Initialize DataTable
  $('#appointmentsTable').DataTable({
    responsive: true,
    pageLength: 10,
    order: [[2, 'desc']],
    language: {
      search: "Search appointments:",
      lengthMenu: "Show _MENU_ entries",
      info: "Showing _START_ to _END_ of _TOTAL_ appointments",
      paginate: {
        first: "First",
        last: "Last",
        next: "Next",
        previous: "Previous"
      }
    }
  });

  // SweetAlert2 for Approve
  $('.approve').click(function() {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Approve Appointment?',
      text: 'Do you want to approve this appointment?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#B4DEBD',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, approve it!',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        $.post('approve.php', { id: id }, function() {
          Swal.fire({
            title: 'Approved!',
            text: 'The appointment has been approved.',
            icon: 'success',
            confirmButtonColor: '#B4DEBD'
          }).then(() => location.reload());
        });
      }
    });
  });

  // SweetAlert2 for Deny (with reason)
  $('.deny').click(async function() {
    const id = $(this).data('id');

    const { value: reasonChoice } = await Swal.fire({
      title: 'Deny Appointment?',
      text: 'Select a reason for denying this appointment:',
      input: 'select',
      inputOptions: {
        'Automated Messages': {
          'conflict': 'Conflict with another appointment schedule',
          'policy': 'Does not comply with clinic policies',
          'info': 'Incomplete or invalid appointment information',
          'late': 'Requested too late or outside clinic hours',
          'other': 'Other (enter manually)'
        }
      },
      inputPlaceholder: 'Select a reason',
      showCancelButton: true,
      confirmButtonText: 'Continue',
      confirmButtonColor: '#80A1BA',
      cancelButtonColor: '#d33'
    });

    if (!reasonChoice) return;

    let reasonText = '';

    if (reasonChoice === 'other') {
      const { value: customReason } = await Swal.fire({
        title: 'Custom Reason',
        input: 'text',
        inputPlaceholder: 'Enter your reason...',
        showCancelButton: true,
        confirmButtonColor: '#80A1BA'
      });
      if (!customReason) return;
      reasonText = customReason;
    } else {
      reasonText = {
        'conflict': 'Conflict with another appointment schedule.',
        'policy': 'Does not comply with clinic policies.',
        'info': 'Incomplete or invalid appointment information.',
        'late': 'Requested too late or outside clinic hours.'
      }[reasonChoice];
    }

    // Final confirmation
    Swal.fire({
      title: 'Confirm Denial',
      html: `<p>Reason:</p><strong>${reasonText}</strong>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#f44336',
      cancelButtonColor: '#80A1BA',
      confirmButtonText: 'Yes, deny it!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.post('deny.php', { id: id, reason: reasonText }, function() {
          Swal.fire({
            title: 'Denied!',
            text: 'The appointment has been denied.',
            icon: 'success',
            confirmButtonColor: '#80A1BA'
          }).then(() => location.reload());
        });
      }
    });
  });
});
</script>

</body>
</html>