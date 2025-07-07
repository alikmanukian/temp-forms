## Image placeholder
if we want to use placeholder image, we can use the following syntax:
It will automatically generate a placeholder image from the original image (small and blurred).

```vue
https://endpoint/images/{image}-placeholder.{extension}


```vue
<template>
  <img src="/images/001.jpg" placeholder="/images/001-placeholder.jpg"/>
</template> 
```

## Lazy loading image
If we want to load image only when it is in the viewport, we can use the following syntax:

```vue
<template>
  <img src="/images/001.jpg" 
       placeholder="/images/placeholder/001.jpg"
       lazy
  />
</template> 
```

## Fetch priority image
If we want to load image with high priority, we can use the following syntax:

```vue
<template>
    <img src="/images/001.jpg"
         priority="high"
    />
</template> 
```

There are three priority levels: `low`, `high` and `auto`. The default is `auto`.

## TODO
config to set placeholder image automatically
config to set width for placeholder image
config to set blur for placeholder image
add lightbox support
add srcset support automatically
add picture tag support
















## Image transformations
https://endpoint/{transformations}/{image}
https://ik.imagekit.io/demo/tr:w-300,h-300/example-asset.jpg

w-{width} - set width
h-{height} - set height
ar-{width}-{height} - set aspect ratio

## Image crop 

### Pad resize strategy (cm-pad_resize)
example - set bg color on pad: `?w-300,h-200,cm-pad_resize,bg-f3f3f3`
example - set padding on right: `?w-300,h-200,cm-pad_resize,bg-f3f3f3,fo-left`
example - set padding on left: `?w-300,h-200,cm-pad_resize,bg-f3f3f3,fo-right`
example - set padding on top: `?w-300,h-200,cm-pad_resize,bg-f3f3f3,fo-bottom`
example - set padding on bottom: `?w-300,h-200,cm-pad_resize,bg-f3f3f3,fo-top`

### Force crop strategy (cm-pad_resize)
example: `?w-300,h-200,c-force`

### Max size crop strategy (cm-max_size)
example: `?w-300,h-200,c-at_max`

### Max size enlarge crop strategy

### Min size crop strategy


