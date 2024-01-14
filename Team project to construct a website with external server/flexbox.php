<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flexbox test</title>

<style>

.flex-parent { /* css for parent flexbox */
  display: flex;
  flex-direction: row; /* The flexbox goes from left to right. */
  justify-content: center; /* The boxes are centered horizontally */
  align-items: flex-start; /* Each box starts at the top (vertically). */
  column-gap: 70px; /* The space between each box. */
}

.flex-child { /* css for children flexboxes */
  flex-basis: 50%;
  background: teal;
  font-size: 3em;
  font-weight: bold;
  text-align: center;
}

</style>

</head>
<body>

<div class="flex-parent">
    <div class="flex-child">
      Hi
    </div>
    <div class="flex-child">
      Ok
    </div>
</div>

</body>
</html>