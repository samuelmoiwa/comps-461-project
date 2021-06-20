<?php

function display_footer()
{
    echo '
         <script src="./js/jquery.min.js" ></script>
        <script src="./js/popper.js"></script>
        <script src="./js/bootstrap.js"></script>
        <script src="./js/swal.js"></script>
        <script src="./js/main.js"></script>
    </body></html>';
}

function getDosagePlanById($connection, $dosageId)
{

    try {

        $sql = "SELECT * FROM tbl_dosages WHERE dosageId = :dosageId";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":dosageId", $dosageId, PDO::PARAM_INT);
        if ($stmt->execute()) {

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } else {
            return null;
        }

    } catch (Exception $ex) {
        throw $ex;
    } finally {
        $connection = null;
    }

}