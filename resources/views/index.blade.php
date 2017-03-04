<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Happy Xur Day!</title>
  <link href="https://fonts.googleapis.com/css?family=Karla:400,400i,700" rel="stylesheet">
  <link rel="stylesheet" href="/css/app.css">
</head>
<body>
  <div class="app-container">
    <div class="app-container-inner">
      <h1>Xûr</h1>
      <h2>Agent of the Nine</h2>
      <div class="countdown" data-arrival="{{ $arrival }}" data-departure="{{ $departure }}" data-present="{{ $present }}"></div>
      <p>{{ $location }}</p>
      <div class="inventory-container">
        @foreach ($inventory as $category => $items)
          <h3 class="inventory-category">{{ $category }}</h3>
          <ul class="inventory-items">
            @foreach ($items as $item)
              <li class="inventory-item">
                <a href="https://bungie.net/en/Armory/Detail?item={{ $item['item']['itemHash'] }}" target="_blank" rel="noopener">
                  <span class="icon-wrap">
                    <img src="https://bungie.net{{ $item['item']['icon'] }}" alt="{{ $item['item']['itemName'] }}" class="inventory-item-icon" />
                    @if($item['stackSize'] > 1)
                      <span class="item-stacks">{{ $item['stackSize'] }}</span>
                    @endif
                  </span>
                  <span class="item-meta">
                    <h4 class="item-name">{{ $item['item']['itemName'] }}</h4>
                    <p class="item-type">{{ $item['item']['itemTypeName'] }}</p>
                  </span>
                </a>
              </li>
            @endforeach
          </ul>
        @endforeach
      </div>
    </div>
  </div>
  <script src="js/app.js"></script>
</body>
</html>
