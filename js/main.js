$(document).ready(() => {
  let btnDeleteDosagePlan = document.querySelectorAll("#btnDeleteDosagePlan");
  let btnUpdateDosagePlan = document.querySelectorAll("#btnUpdateDosagePlan");
  let btnDeleteMedicine = document.querySelectorAll("#btnDeleteMedicine");
  let btnUpdateMedicine = document.querySelectorAll("#btnUpdateMedicine");

  const urlParams = new URLSearchParams(window.location.search);

  let request;
  let isDosagePlanUpdate = false;
  let dosagePlanID = 0;

  btnUpdateDosagePlan.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      dosagePlanID = e.target.getAttribute("data-id");

      $.ajax({
        url: `./update.php`,
        method: "GET",
        data: { dosageId: dosagePlanID },
        success: function (response) {
          let data = JSON.parse(response);
          const { dateTaken, medicineId, dosageId, timeTaken, userId } = data;

          $("#dateTaken").val(dateTaken);
          $("#timeTaken").val(timeTaken);
          $("#medicineId").val(medicineId);
          $("#dosageId").val(dosageId);
          $("#userId").val(userId);
          isDosagePlanUpdate = true;
          $("#dosageModal").modal("show");
        },
        failure: function (response) {
          console.log(response);
        },
      });
    });
  });

  btnDeleteMedicine.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      swal({
        title: "Are you sure you want to delete this medicine?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          let medicine_id = e.target.getAttribute("data-id");

          $.ajax({
            url: `./delete.php`,
            method: "POST",
            data: { medicine_id },
            success: function (response) {
              let data = JSON.parse(response);
              if (data.success) {
                swal(`${data.success}`, {
                  icon: "success",
                }).then(() => {
                  const page = urlParams.get("page");
                  location.assign(
                    `${page != null ? `./home.php?page=${page}` : `./home.php`}`
                  );
                });
              } else if (data.error) {
                swal(`${data.error}`, {
                  icon: "error",
                });
              } else {
                const { errors } = data;
                console.log(errors);
              }
            },
            failure: function (response) {
              console.log(response);
            },
          });
        } else {
          // swal("Your imaginary file is safe!");
        }
      });
    });
  });

  btnUpdateMedicine.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      let medicine_id = e.target.getAttribute("data-id");
    });
  });
});
