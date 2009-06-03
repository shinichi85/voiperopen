I found the best way to create a clean 14-color splash image from grub.png was to:
1. gimp jnode/gui/images/background.png

2. Use the eyedropper (Tools > Color Picker) to sample the blue color (#4682b4) as the foreground color in GIMP.

3. File > Open grub.png

4. Use the bucket fill (Tools > Paint > Bucket Fill) to replace the transparent area around the logo with the Jnode desktop blue color.

5. Squish the image down to 640x480 splash image size with Image > Scale > 640 x 480, Interpolation: Cubic

6. Manually "posterize" the image with Image > Mode > Indexed, 8 colors, No dithering, No transparent dithering
Note: I chose 8 colors to force the hands and hair to one solid color and avoid the "banding" effect you normally get with RGB images cut down to a small indexed range, or when you use Gimp's included Posterize function.

7. Convert back to RGB with Image > Mode > RGB (as setup for the next step)

8. Convert down to the final 14-color indexed splash image format with Image > Mode > Indexed, 14 colors, No dithering, No transparent dithering

9. It's art, not science. So, you might have to play around with Gimp until you are happy with final output.

10. Save As an actual splash image file with File > Save As, grublogo.xpm and exit Gimp.

11. Compress the grublogo.xpm with gzip -9v grublogo.xpm

Now you have a "boot-ready" GRUB logo... all you need is a GRUB which can show it!
