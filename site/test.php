<?php

  /*
   * първи вариант
   */

  $query_min_max_ids = "SELECT @min := MIN(`product_id`) as min,@max := MAX(`product_id`) as max FROM `products`";
  $result_min_max_ids = mysqli_query($db_link, $query_min_max_ids);
  if(!$result_min_max_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_min_max_ids) > 0) {
    $min_max_ids_row = mysqli_fetch_assoc($result_min_max_ids);
    $min_id = $min_max_ids_row['min'];
    $max_id = $min_max_ids_row['max'];
  }

  $query_random_prod_ids = "SELECT a.`product_id`
                            FROM `products` a
                            JOIN ( SELECT `product_id` FROM
                                    ( SELECT `product_id`
                                        FROM ( SELECT $min_id + ($max_id - $min_id + 1 - 5) * RAND() AS start FROM DUAL ) AS init
                                        JOIN `products` y
                                        WHERE    y.`product_id` > init.start AND y.`product_is_active` = '1'
                                        ORDER BY y.`product_id`
                                        LIMIT 50 
                                    ) z ORDER BY RAND()
                                   LIMIT $count 
                                 ) r ON a.`product_id` = r.`product_id`";
  //echo $query_random_prod_ids."<br>";
  $result_random_prod_ids = mysqli_query($db_link, $query_random_prod_ids);
  if(!$result_random_prod_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_random_prod_ids) > 0) {
    while($random_prod_ids_row = mysqli_fetch_assoc($result_random_prod_ids)) {
      $random_prod_ids[] = $random_prod_ids_row['product_id'];
    }
  }
  
  /*
   * втори вариант
   */
  
  $query_random_prod_ids = "SELECT r1.`product_id`,r1.`product_is_active`
                            FROM `products` AS r1 JOIN
                                 (SELECT CEIL(RAND() *
                                               (SELECT MAX(`product_id`)
                                                  FROM `products`)) AS id)
                                  AS r2
                           WHERE r1.`product_id` >= r2.id AND r1.`product_is_active` = '1'
                           LIMIT $count";
  //echo $query_random_prod_ids."<br>";
  $result_random_prod_ids = mysqli_query($db_link, $query_random_prod_ids);
  if(!$result_random_prod_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_random_prod_ids) > 0) {
    while($random_prod_ids_row = mysqli_fetch_assoc($result_random_prod_ids)) {
      $random_prod_ids[] = $random_prod_ids_row['product_id'];
    }
  }
  else {
    $random_prod_ids = array(0,mt_rand(0, 63));
  }