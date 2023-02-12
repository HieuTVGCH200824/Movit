<div style="max-width: 70%;margin-left: auto; margin-right: auto">
    <table border="1">
        <thead>
            <tr>
                <th>No. </th>
                <th>Origin</th>
                <th>Destination</th>
                <th>Distance</th>
                <th>Price</th>
                <th>Total</th>
                <th>Google Origin</th>
                <th>Google Destination</th>
                <th>Status</th>
                <th>Message</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($sheets as $s) {
            ?>
            <tr>
                <td><?= $s->no ?></td>
                <td><?= $s->origin ?></td>
                <td><?= $s->destination ?></td>
                <td><?= $s->distance ?></td>
                <td><?= $s->price ?> VND</td>
                <td><?= $s->total ?> VND</td>
                <td><?= $s->googleOrigin ?></td>
                <td><?= $s->googleDestination ?></td>
                <td><?= $s->status ?></td>
                <td><?= $s->message ?></td>
                <td>
                    <form Method="POST">
                        <input type='hidden' name='number' value='<?php echo $s->no ?>' />
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>