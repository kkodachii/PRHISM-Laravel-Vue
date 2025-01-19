// vite.config.js
import { defineConfig } from "file:///C:/Users/Administrator/Documents/Laravel_Projects/web_prhism_paombong/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/Users/Administrator/Documents/Laravel_Projects/web_prhism_paombong/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/Users/Administrator/Documents/Laravel_Projects/web_prhism_paombong/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import os from "os";
import { VitePWA } from "file:///C:/Users/Administrator/Documents/Laravel_Projects/web_prhism_paombong/node_modules/vite-plugin-pwa/dist/index.js";
function getLocalIP() {
  const interfaces = os.networkInterfaces();
  for (const name of Object.keys(interfaces)) {
    for (const iface of interfaces[name]) {
      if (iface.family === "IPv4" && !iface.internal) {
        return iface.address;
      }
    }
  }
  return "127.0.0.1";
}
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: "resources/js/app.js",
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    VitePWA({
      registerType: "autoUpdate",
      manifest: {
        "name": "PRHISM",
        "short_name": "PRHISM",
        "description": "",
        "start_url": "http://localhost:8000",
        "display": "standalone",
        "background_color": "#ebeaea",
        "theme_color": "#f9fafb",
        "orientation": "portrait-primary",
        "icons": [
          {
            "src": "/images/icons/windows11/SmallTile.scale-100.png",
            "type": "image/png",
            "sizes": "71x71"
          },
          {
            "src": "/images/icons/windows11/SmallTile.scale-125.png",
            "type": "image/png",
            "sizes": "89x89"
          },
          {
            "src": "/images/icons/windows11/SmallTile.scale-150.png",
            "type": "image/png",
            "sizes": "107x107"
          },
          {
            "src": "/images/icons/windows11/SmallTile.scale-200.png",
            "type": "image/png",
            "sizes": "142x142"
          },
          {
            "src": "/images/icons/windows11/SmallTile.scale-400.png",
            "type": "image/png",
            "sizes": "284x284"
          },
          {
            "src": "/images/icons/windows11/Square150x150Logo.scale-100.png",
            "type": "image/png",
            "sizes": "150x150"
          },
          {
            "src": "/images/icons/windows11/Square150x150Logo.scale-125.png",
            "type": "image/png",
            "sizes": "188x188"
          },
          {
            "src": "/images/icons/windows11/Square150x150Logo.scale-150.png",
            "type": "image/png",
            "sizes": "225x225"
          },
          {
            "src": "/images/icons/windows11/Square150x150Logo.scale-200.png",
            "type": "image/png",
            "sizes": "300x300"
          },
          {
            "src": "/images/icons/windows11/Square150x150Logo.scale-400.png",
            "type": "image/png",
            "sizes": "600x600"
          },
          {
            "src": "/images/icons/windows11/Wide310x150Logo.scale-100.png",
            "type": "image/png",
            "sizes": "310x150"
          },
          {
            "src": "/images/icons/windows11/Wide310x150Logo.scale-125.png",
            "type": "image/png",
            "sizes": "388x188"
          },
          {
            "src": "/images/icons/windows11/Wide310x150Logo.scale-150.png",
            "type": "image/png",
            "sizes": "465x225"
          },
          {
            "src": "/images/icons/windows11/Wide310x150Logo.scale-200.png",
            "type": "image/png",
            "sizes": "620x300"
          },
          {
            "src": "/images/icons/windows11/Wide310x150Logo.scale-400.png",
            "type": "image/png",
            "sizes": "1240x600"
          },
          {
            "src": "/images/icons/windows11/LargeTile.scale-100.png",
            "type": "image/png",
            "sizes": "310x310"
          },
          {
            "src": "/images/icons/windows11/LargeTile.scale-125.png",
            "type": "image/png",
            "sizes": "388x388"
          },
          {
            "src": "/images/icons/windows11/LargeTile.scale-150.png",
            "type": "image/png",
            "sizes": "465x465"
          },
          {
            "src": "/images/icons/windows11/LargeTile.scale-200.png",
            "type": "image/png",
            "sizes": "620x620"
          },
          {
            "src": "/images/icons/windows11/LargeTile.scale-400.png",
            "type": "image/png",
            "sizes": "1240x1240"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.scale-100.png",
            "type": "image/png",
            "sizes": "44x44"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.scale-125.png",
            "type": "image/png",
            "sizes": "55x55"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.scale-150.png",
            "type": "image/png",
            "sizes": "66x66"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.scale-200.png",
            "type": "image/png",
            "sizes": "88x88"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.scale-400.png",
            "type": "image/png",
            "sizes": "176x176"
          },
          {
            "src": "/images/icons/windows11/StoreLogo.scale-100.png",
            "type": "image/png",
            "sizes": "50x50"
          },
          {
            "src": "/images/icons/windows11/StoreLogo.scale-125.png",
            "type": "image/png",
            "sizes": "63x63"
          },
          {
            "src": "/images/icons/windows11/StoreLogo.scale-150.png",
            "type": "image/png",
            "sizes": "75x75"
          },
          {
            "src": "/images/icons/windows11/StoreLogo.scale-200.png",
            "type": "image/png",
            "sizes": "100x100"
          },
          {
            "src": "/images/icons/windows11/StoreLogo.scale-400.png",
            "type": "image/png",
            "sizes": "200x200"
          },
          {
            "src": "/images/icons/windows11/SplashScreen.scale-100.png",
            "type": "image/png",
            "sizes": "620x300"
          },
          {
            "src": "/images/icons/windows11/SplashScreen.scale-125.png",
            "type": "image/png",
            "sizes": "775x375"
          },
          {
            "src": "/images/icons/windows11/SplashScreen.scale-150.png",
            "type": "image/png",
            "sizes": "930x450"
          },
          {
            "src": "/images/icons/windows11/SplashScreen.scale-200.png",
            "type": "image/png",
            "sizes": "1240x600"
          },
          {
            "src": "/images/icons/windows11/SplashScreen.scale-400.png",
            "type": "image/png",
            "sizes": "2480x1200"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-16.png",
            "type": "image/png",
            "sizes": "16x16"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-20.png",
            "type": "image/png",
            "sizes": "20x20"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-24.png",
            "type": "image/png",
            "sizes": "24x24"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-30.png",
            "type": "image/png",
            "sizes": "30x30"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-32.png",
            "type": "image/png",
            "sizes": "32x32"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-36.png",
            "type": "image/png",
            "sizes": "36x36"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-40.png",
            "type": "image/png",
            "sizes": "40x40"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-44.png",
            "type": "image/png",
            "sizes": "44x44"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-48.png",
            "type": "image/png",
            "sizes": "48x48"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-60.png",
            "type": "image/png",
            "sizes": "60x60"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-64.png",
            "type": "image/png",
            "sizes": "64x64"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-72.png",
            "type": "image/png",
            "sizes": "72x72"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-80.png",
            "type": "image/png",
            "sizes": "80x80"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-96.png",
            "type": "image/png",
            "sizes": "96x96"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.targetsize-256.png",
            "type": "image/png",
            "sizes": "256x256"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-16.png",
            "type": "image/png",
            "sizes": "16x16"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-20.png",
            "type": "image/png",
            "sizes": "20x20"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-24.png",
            "type": "image/png",
            "sizes": "24x24"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-30.png",
            "type": "image/png",
            "sizes": "30x30"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-32.png",
            "type": "image/png",
            "sizes": "32x32"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-36.png",
            "type": "image/png",
            "sizes": "36x36"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-40.png",
            "type": "image/png",
            "sizes": "40x40"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-44.png",
            "type": "image/png",
            "sizes": "44x44"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-48.png",
            "type": "image/png",
            "sizes": "48x48"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-60.png",
            "type": "image/png",
            "sizes": "60x60"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-64.png",
            "type": "image/png",
            "sizes": "64x64"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-72.png",
            "type": "image/png",
            "sizes": "72x72"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-80.png",
            "type": "image/png",
            "sizes": "80x80"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-96.png",
            "type": "image/png",
            "sizes": "96x96"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-unplated_targetsize-256.png",
            "type": "image/png",
            "sizes": "256x256"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-16.png",
            "type": "image/png",
            "sizes": "16x16"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-20.png",
            "type": "image/png",
            "sizes": "20x20"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-24.png",
            "type": "image/png",
            "sizes": "24x24"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-30.png",
            "type": "image/png",
            "sizes": "30x30"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-32.png",
            "type": "image/png",
            "sizes": "32x32"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-36.png",
            "type": "image/png",
            "sizes": "36x36"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-40.png",
            "type": "image/png",
            "sizes": "40x40"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-44.png",
            "type": "image/png",
            "sizes": "44x44"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-48.png",
            "type": "image/png",
            "sizes": "48x48"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-60.png",
            "type": "image/png",
            "sizes": "60x60"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-64.png",
            "type": "image/png",
            "sizes": "64x64"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-72.png",
            "type": "image/png",
            "sizes": "72x72"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-80.png",
            "type": "image/png",
            "sizes": "80x80"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-96.png",
            "type": "image/png",
            "sizes": "96x96"
          },
          {
            "src": "/images/icons/windows11/Square44x44Logo.altform-lightunplated_targetsize-256.png",
            "type": "image/png",
            "sizes": "256x256"
          },
          {
            "src": "/images/icons/android/android-launchericon-512-512.png",
            "type": "image/png",
            "sizes": "512x512",
            "purpose": "any"
          },
          {
            "src": "/images/icons/android/android-launchericon-192-192.png",
            "type": "image/png",
            "sizes": "192x192"
          },
          {
            "src": "/images/icons/android/android-launchericon-144-144.png",
            "type": "image/png",
            "sizes": "144x144"
          },
          {
            "src": "/images/icons/android/android-launchericon-96-96.png",
            "type": "image/png",
            "sizes": "96x96"
          },
          {
            "src": "/images/icons/android/android-launchericon-72-72.png",
            "type": "image/png",
            "sizes": "72x72"
          },
          {
            "src": "/images/icons/android/android-launchericon-48-48.png",
            "type": "image/png",
            "sizes": "48x48"
          },
          {
            "src": "/images/icons/ios/16.png",
            "type": "image/png",
            "sizes": "16x16"
          },
          {
            "src": "/images/icons/ios/20.png",
            "type": "image/png",
            "sizes": "20x20"
          },
          {
            "src": "/images/icons/ios/29.png",
            "type": "image/png",
            "sizes": "29x29"
          },
          {
            "src": "/images/icons/ios/32.png",
            "type": "image/png",
            "sizes": "32x32"
          },
          {
            "src": "/images/icons/ios/40.png",
            "type": "image/png",
            "sizes": "40x40"
          },
          {
            "src": "/images/icons/ios/50.png",
            "type": "image/png",
            "sizes": "50x50"
          },
          {
            "src": "/images/icons/ios/57.png",
            "type": "image/png",
            "sizes": "57x57"
          },
          {
            "src": "/images/icons/ios/58.png",
            "type": "image/png",
            "sizes": "58x58"
          },
          {
            "src": "/images/icons/ios/60.png",
            "type": "image/png",
            "sizes": "60x60"
          },
          {
            "src": "/images/icons/ios/64.png",
            "type": "image/png",
            "sizes": "64x64"
          },
          {
            "src": "/images/icons/ios/72.png",
            "type": "image/png",
            "sizes": "72x72"
          },
          {
            "src": "/images/icons/ios/76.png",
            "type": "image/png",
            "sizes": "76x76"
          },
          {
            "src": "/images/icons/ios/80.png",
            "type": "image/png",
            "sizes": "80x80"
          },
          {
            "src": "/images/icons/ios/87.png",
            "type": "image/png",
            "sizes": "87x87"
          },
          {
            "src": "/images/icons/ios/100.png",
            "type": "image/png",
            "sizes": "100x100"
          },
          {
            "src": "/images/icons/ios/114.png",
            "type": "image/png",
            "sizes": "114x114"
          },
          {
            "src": "/images/icons/ios/120.png",
            "type": "image/png",
            "sizes": "120x120"
          },
          {
            "src": "/images/icons/ios/128.png",
            "type": "image/png",
            "sizes": "128x128"
          },
          {
            "src": "/images/icons/ios/144.png",
            "type": "image/png",
            "sizes": "144x144"
          },
          {
            "src": "/images/icons/ios/152.png",
            "type": "image/png",
            "sizes": "152x152"
          },
          {
            "src": "/images/icons/ios/167.png",
            "type": "image/png",
            "sizes": "167x167"
          },
          {
            "src": "/images/icons/ios/180.png",
            "type": "image/png",
            "sizes": "180x180"
          },
          {
            "src": "/images/icons/ios/192.png",
            "type": "image/png",
            "sizes": "192x192"
          },
          {
            "src": "/images/icons/ios/256.png",
            "type": "image/png",
            "sizes": "256x256"
          },
          {
            "src": "/images/icons/ios/512.png",
            "type": "image/png",
            "sizes": "512x512"
          },
          {
            "src": "/images/icons/ios/1024.png",
            "type": "image/png",
            "sizes": "1024x1024"
          },
          {
            "src": "/images/icons/maskable-icons/48.png",
            "type": "image/png",
            "sizes": "48x48",
            "purpose": "maskable"
          },
          {
            "src": "/images/icons/maskable-icons/72.png",
            "type": "image/png",
            "sizes": "72x72",
            "purpose": "maskable"
          },
          {
            "src": "/images/icons/maskable-icons/96.png",
            "type": "image/png",
            "sizes": "96x96",
            "purpose": "maskable"
          },
          {
            "src": "/images/icons/maskable-icons/128.png",
            "type": "image/png",
            "sizes": "128x128",
            "purpose": "maskable"
          },
          {
            "src": "/images/icons/maskable-icons/192.png",
            "type": "image/png",
            "sizes": "192x192",
            "purpose": "maskable"
          },
          {
            "src": "/images/icons/maskable-icons/384.png",
            "type": "image/png",
            "sizes": "384x384",
            "purpose": "maskable"
          },
          {
            "src": "/images/icons/maskable-icons/512.png",
            "type": "image/png",
            "sizes": "512x512",
            "purpose": "maskable"
          }
        ]
      },
      workbox: {
        runtimeCaching: [
          {
            urlPattern: /\/.*\.(?:png|jpg|jpeg|svg|gif|css|js)$/,
            handler: "CacheFirst",
            options: {
              cacheName: "assets-cache",
              expiration: {
                maxEntries: 50,
                maxAgeSeconds: 30 * 24 * 60 * 60
                // 30 Days
              }
            }
          }
        ]
      }
    })
  ],
  server: {
    host: "0.0.0.0",
    hmr: {
      host: getLocalIP()
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxBZG1pbmlzdHJhdG9yXFxcXERvY3VtZW50c1xcXFxMYXJhdmVsX1Byb2plY3RzXFxcXHdlYl9wcmhpc21fcGFvbWJvbmdcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZmlsZW5hbWUgPSBcIkM6XFxcXFVzZXJzXFxcXEFkbWluaXN0cmF0b3JcXFxcRG9jdW1lbnRzXFxcXExhcmF2ZWxfUHJvamVjdHNcXFxcd2ViX3ByaGlzbV9wYW9tYm9uZ1xcXFx2aXRlLmNvbmZpZy5qc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9pbXBvcnRfbWV0YV91cmwgPSBcImZpbGU6Ly8vQzovVXNlcnMvQWRtaW5pc3RyYXRvci9Eb2N1bWVudHMvTGFyYXZlbF9Qcm9qZWN0cy93ZWJfcHJoaXNtX3Bhb21ib25nL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJztcbmltcG9ydCB2dWUgZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlJztcbmltcG9ydCBvcyBmcm9tICdvcyc7XG5pbXBvcnQgeyBWaXRlUFdBIH0gZnJvbSAndml0ZS1wbHVnaW4tcHdhJztcblxuZnVuY3Rpb24gZ2V0TG9jYWxJUCgpIHtcbiAgICBjb25zdCBpbnRlcmZhY2VzID0gb3MubmV0d29ya0ludGVyZmFjZXMoKTtcbiAgICBmb3IgKGNvbnN0IG5hbWUgb2YgT2JqZWN0LmtleXMoaW50ZXJmYWNlcykpIHtcbiAgICAgIGZvciAoY29uc3QgaWZhY2Ugb2YgaW50ZXJmYWNlc1tuYW1lXSkge1xuICAgICAgICBpZiAoaWZhY2UuZmFtaWx5ID09PSAnSVB2NCcgJiYgIWlmYWNlLmludGVybmFsKSB7XG4gICAgICAgICAgcmV0dXJuIGlmYWNlLmFkZHJlc3M7XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuICcxMjcuMC4wLjEnOyAvLyBGYWxsYmFjayB0byBsb2NhbGhvc3QgaWYgbm8gSVAgZm91bmRcbiAgfVxuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICAgIHBsdWdpbnM6IFtcbiAgICAgICAgbGFyYXZlbCh7XG4gICAgICAgICAgICBpbnB1dDogJ3Jlc291cmNlcy9qcy9hcHAuanMnLFxuICAgICAgICAgICAgcmVmcmVzaDogdHJ1ZSxcbiAgICAgICAgfSksXG4gICAgICAgIHZ1ZSh7XG4gICAgICAgICAgICB0ZW1wbGF0ZToge1xuICAgICAgICAgICAgICAgIHRyYW5zZm9ybUFzc2V0VXJsczoge1xuICAgICAgICAgICAgICAgICAgICBiYXNlOiBudWxsLFxuICAgICAgICAgICAgICAgICAgICBpbmNsdWRlQWJzb2x1dGU6IGZhbHNlLFxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9LFxuICAgICAgICB9KSxcbiAgICAgICAgVml0ZVBXQSh7XG4gICAgICAgICAgICByZWdpc3RlclR5cGU6ICdhdXRvVXBkYXRlJyxcbiAgICAgICAgICAgIG1hbmlmZXN0OiB7XG4gICAgICAgICAgICAgICAgXCJuYW1lXCI6IFwiUFJISVNNXCIsXG4gICAgICAgICAgICAgICAgXCJzaG9ydF9uYW1lXCI6IFwiUFJISVNNXCIsXG4gICAgICAgICAgICAgICAgXCJkZXNjcmlwdGlvblwiOiBcIlwiLFxuICAgICAgICAgICAgICAgIFwic3RhcnRfdXJsXCI6IFwiaHR0cDovL2xvY2FsaG9zdDo4MDAwXCIsXG4gICAgICAgICAgICAgICAgXCJkaXNwbGF5XCI6IFwic3RhbmRhbG9uZVwiLFxuICAgICAgICAgICAgICAgIFwiYmFja2dyb3VuZF9jb2xvclwiOiBcIiNlYmVhZWFcIixcbiAgICAgICAgICAgICAgICBcInRoZW1lX2NvbG9yXCI6IFwiI2Y5ZmFmYlwiLFxuICAgICAgICAgICAgICAgIFwib3JpZW50YXRpb25cIjogXCJwb3J0cmFpdC1wcmltYXJ5XCIsXG4gICAgICAgICAgICAgICAgXCJpY29uc1wiOiBbXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU21hbGxUaWxlLnNjYWxlLTEwMC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjcxeDcxXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TbWFsbFRpbGUuc2NhbGUtMTI1LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiODl4ODlcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NtYWxsVGlsZS5zY2FsZS0xNTAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxMDd4MTA3XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TbWFsbFRpbGUuc2NhbGUtMjAwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMTQyeDE0MlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU21hbGxUaWxlLnNjYWxlLTQwMC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjI4NHgyODRcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTE1MHgxNTBMb2dvLnNjYWxlLTEwMC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjE1MHgxNTBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTE1MHgxNTBMb2dvLnNjYWxlLTEyNS5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjE4OHgxODhcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTE1MHgxNTBMb2dvLnNjYWxlLTE1MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjIyNXgyMjVcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTE1MHgxNTBMb2dvLnNjYWxlLTIwMC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjMwMHgzMDBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTE1MHgxNTBMb2dvLnNjYWxlLTQwMC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjYwMHg2MDBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1dpZGUzMTB4MTUwTG9nby5zY2FsZS0xMDAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIzMTB4MTUwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9XaWRlMzEweDE1MExvZ28uc2NhbGUtMTI1LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMzg4eDE4OFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvV2lkZTMxMHgxNTBMb2dvLnNjYWxlLTE1MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjQ2NXgyMjVcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1dpZGUzMTB4MTUwTG9nby5zY2FsZS0yMDAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI2MjB4MzAwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9XaWRlMzEweDE1MExvZ28uc2NhbGUtNDAwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMTI0MHg2MDBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL0xhcmdlVGlsZS5zY2FsZS0xMDAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIzMTB4MzEwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9MYXJnZVRpbGUuc2NhbGUtMTI1LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMzg4eDM4OFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvTGFyZ2VUaWxlLnNjYWxlLTE1MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjQ2NXg0NjVcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL0xhcmdlVGlsZS5zY2FsZS0yMDAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI2MjB4NjIwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9MYXJnZVRpbGUuc2NhbGUtNDAwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMTI0MHgxMjQwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uc2NhbGUtMTAwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiNDR4NDRcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5zY2FsZS0xMjUucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI1NXg1NVwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnNjYWxlLTE1MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjY2eDY2XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uc2NhbGUtMjAwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiODh4ODhcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5zY2FsZS00MDAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxNzZ4MTc2XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TdG9yZUxvZ28uc2NhbGUtMTAwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiNTB4NTBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1N0b3JlTG9nby5zY2FsZS0xMjUucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI2M3g2M1wiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3RvcmVMb2dvLnNjYWxlLTE1MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjc1eDc1XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TdG9yZUxvZ28uc2NhbGUtMjAwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMTAweDEwMFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3RvcmVMb2dvLnNjYWxlLTQwMC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjIwMHgyMDBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NwbGFzaFNjcmVlbi5zY2FsZS0xMDAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI2MjB4MzAwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcGxhc2hTY3JlZW4uc2NhbGUtMTI1LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiNzc1eDM3NVwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3BsYXNoU2NyZWVuLnNjYWxlLTE1MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjkzMHg0NTBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NwbGFzaFNjcmVlbi5zY2FsZS0yMDAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxMjQweDYwMFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3BsYXNoU2NyZWVuLnNjYWxlLTQwMC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjI0ODB4MTIwMFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtMTYucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxNngxNlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtMjAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIyMHgyMFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtMjQucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIyNHgyNFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtMzAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIzMHgzMFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtMzIucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIzMngzMlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtMzYucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIzNngzNlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtNDAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI0MHg0MFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtNDQucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI0NHg0NFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtNDgucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI0OHg0OFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtNjAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI2MHg2MFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtNjQucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI2NHg2NFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtNzIucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI3Mng3MlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtODAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI4MHg4MFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtOTYucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI5Nng5NlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLnRhcmdldHNpemUtMjU2LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMjU2eDI1NlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLmFsdGZvcm0tdW5wbGF0ZWRfdGFyZ2V0c2l6ZS0xNi5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjE2eDE2XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uYWx0Zm9ybS11bnBsYXRlZF90YXJnZXRzaXplLTIwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMjB4MjBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5hbHRmb3JtLXVucGxhdGVkX3RhcmdldHNpemUtMjQucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIyNHgyNFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLmFsdGZvcm0tdW5wbGF0ZWRfdGFyZ2V0c2l6ZS0zMC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjMweDMwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uYWx0Zm9ybS11bnBsYXRlZF90YXJnZXRzaXplLTMyLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMzJ4MzJcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5hbHRmb3JtLXVucGxhdGVkX3RhcmdldHNpemUtMzYucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIzNngzNlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLmFsdGZvcm0tdW5wbGF0ZWRfdGFyZ2V0c2l6ZS00MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjQweDQwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uYWx0Zm9ybS11bnBsYXRlZF90YXJnZXRzaXplLTQ0LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiNDR4NDRcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5hbHRmb3JtLXVucGxhdGVkX3RhcmdldHNpemUtNDgucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI0OHg0OFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLmFsdGZvcm0tdW5wbGF0ZWRfdGFyZ2V0c2l6ZS02MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjYweDYwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uYWx0Zm9ybS11bnBsYXRlZF90YXJnZXRzaXplLTY0LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiNjR4NjRcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5hbHRmb3JtLXVucGxhdGVkX3RhcmdldHNpemUtNzIucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI3Mng3MlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLmFsdGZvcm0tdW5wbGF0ZWRfdGFyZ2V0c2l6ZS04MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjgweDgwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uYWx0Zm9ybS11bnBsYXRlZF90YXJnZXRzaXplLTk2LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiOTZ4OTZcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5hbHRmb3JtLXVucGxhdGVkX3RhcmdldHNpemUtMjU2LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMjU2eDI1NlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLmFsdGZvcm0tbGlnaHR1bnBsYXRlZF90YXJnZXRzaXplLTE2LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMTZ4MTZcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5hbHRmb3JtLWxpZ2h0dW5wbGF0ZWRfdGFyZ2V0c2l6ZS0yMC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjIweDIwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uYWx0Zm9ybS1saWdodHVucGxhdGVkX3RhcmdldHNpemUtMjQucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIyNHgyNFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLmFsdGZvcm0tbGlnaHR1bnBsYXRlZF90YXJnZXRzaXplLTMwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMzB4MzBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5hbHRmb3JtLWxpZ2h0dW5wbGF0ZWRfdGFyZ2V0c2l6ZS0zMi5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjMyeDMyXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uYWx0Zm9ybS1saWdodHVucGxhdGVkX3RhcmdldHNpemUtMzYucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIzNngzNlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLmFsdGZvcm0tbGlnaHR1bnBsYXRlZF90YXJnZXRzaXplLTQwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiNDB4NDBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5hbHRmb3JtLWxpZ2h0dW5wbGF0ZWRfdGFyZ2V0c2l6ZS00NC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjQ0eDQ0XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uYWx0Zm9ybS1saWdodHVucGxhdGVkX3RhcmdldHNpemUtNDgucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI0OHg0OFwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLmFsdGZvcm0tbGlnaHR1bnBsYXRlZF90YXJnZXRzaXplLTYwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiNjB4NjBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5hbHRmb3JtLWxpZ2h0dW5wbGF0ZWRfdGFyZ2V0c2l6ZS02NC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjY0eDY0XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uYWx0Zm9ybS1saWdodHVucGxhdGVkX3RhcmdldHNpemUtNzIucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI3Mng3MlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy93aW5kb3dzMTEvU3F1YXJlNDR4NDRMb2dvLmFsdGZvcm0tbGlnaHR1bnBsYXRlZF90YXJnZXRzaXplLTgwLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiODB4ODBcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvd2luZG93czExL1NxdWFyZTQ0eDQ0TG9nby5hbHRmb3JtLWxpZ2h0dW5wbGF0ZWRfdGFyZ2V0c2l6ZS05Ni5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjk2eDk2XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL3dpbmRvd3MxMS9TcXVhcmU0NHg0NExvZ28uYWx0Zm9ybS1saWdodHVucGxhdGVkX3RhcmdldHNpemUtMjU2LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMjU2eDI1NlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy9hbmRyb2lkL2FuZHJvaWQtbGF1bmNoZXJpY29uLTUxMi01MTIucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI1MTJ4NTEyXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInB1cnBvc2VcIjogXCJhbnlcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvYW5kcm9pZC9hbmRyb2lkLWxhdW5jaGVyaWNvbi0xOTItMTkyLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMTkyeDE5MlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy9hbmRyb2lkL2FuZHJvaWQtbGF1bmNoZXJpY29uLTE0NC0xNDQucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxNDR4MTQ0XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2FuZHJvaWQvYW5kcm9pZC1sYXVuY2hlcmljb24tOTYtOTYucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI5Nng5NlwiXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic3JjXCI6IFwiL2ltYWdlcy9pY29ucy9hbmRyb2lkL2FuZHJvaWQtbGF1bmNoZXJpY29uLTcyLTcyLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiNzJ4NzJcIlxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICBcInNyY1wiOiBcIi9pbWFnZXMvaWNvbnMvYW5kcm9pZC9hbmRyb2lkLWxhdW5jaGVyaWNvbi00OC00OC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjQ4eDQ4XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8xNi5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjE2eDE2XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8yMC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjIweDIwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8yOS5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjI5eDI5XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8zMi5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjMyeDMyXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy80MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjQweDQwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy81MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjUweDUwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy81Ny5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjU3eDU3XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy81OC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjU4eDU4XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy82MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjYweDYwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy82NC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjY0eDY0XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy83Mi5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjcyeDcyXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy83Ni5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjc2eDc2XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy84MC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjgweDgwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy84Ny5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjg3eDg3XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8xMDAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxMDB4MTAwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8xMTQucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxMTR4MTE0XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8xMjAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxMjB4MTIwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8xMjgucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxMjh4MTI4XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8xNDQucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxNDR4MTQ0XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8xNTIucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxNTJ4MTUyXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8xNjcucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxNjd4MTY3XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8xODAucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxODB4MTgwXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8xOTIucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIxOTJ4MTkyXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8yNTYucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCIyNTZ4MjU2XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy81MTIucG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInR5cGVcIjogXCJpbWFnZS9wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwic2l6ZXNcIjogXCI1MTJ4NTEyXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL2lvcy8xMDI0LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiMTAyNHgxMDI0XCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL21hc2thYmxlLWljb25zLzQ4LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiNDh4NDhcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwicHVycG9zZVwiOiBcIm1hc2thYmxlXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL21hc2thYmxlLWljb25zLzcyLnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiNzJ4NzJcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwicHVycG9zZVwiOiBcIm1hc2thYmxlXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL21hc2thYmxlLWljb25zLzk2LnBuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJ0eXBlXCI6IFwiaW1hZ2UvcG5nXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBcInNpemVzXCI6IFwiOTZ4OTZcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwicHVycG9zZVwiOiBcIm1hc2thYmxlXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL21hc2thYmxlLWljb25zLzEyOC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjEyOHgxMjhcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwicHVycG9zZVwiOiBcIm1hc2thYmxlXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL21hc2thYmxlLWljb25zLzE5Mi5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjE5MngxOTJcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwicHVycG9zZVwiOiBcIm1hc2thYmxlXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL21hc2thYmxlLWljb25zLzM4NC5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjM4NHgzODRcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwicHVycG9zZVwiOiBcIm1hc2thYmxlXCJcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgXCJzcmNcIjogXCIvaW1hZ2VzL2ljb25zL21hc2thYmxlLWljb25zLzUxMi5wbmdcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwidHlwZVwiOiBcImltYWdlL3BuZ1wiLFxuICAgICAgICAgICAgICAgICAgICAgICAgXCJzaXplc1wiOiBcIjUxMng1MTJcIixcbiAgICAgICAgICAgICAgICAgICAgICAgIFwicHVycG9zZVwiOiBcIm1hc2thYmxlXCJcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIF1cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB3b3JrYm94OiB7XG4gICAgICAgICAgICAgIHJ1bnRpbWVDYWNoaW5nOiBbXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgdXJsUGF0dGVybjogL1xcLy4qXFwuKD86cG5nfGpwZ3xqcGVnfHN2Z3xnaWZ8Y3NzfGpzKSQvLFxuICAgICAgICAgICAgICAgICAgaGFuZGxlcjogJ0NhY2hlRmlyc3QnLFxuICAgICAgICAgICAgICAgICAgb3B0aW9uczoge1xuICAgICAgICAgICAgICAgICAgICBjYWNoZU5hbWU6ICdhc3NldHMtY2FjaGUnLFxuICAgICAgICAgICAgICAgICAgICBleHBpcmF0aW9uOiB7XG4gICAgICAgICAgICAgICAgICAgICAgbWF4RW50cmllczogNTAsXG4gICAgICAgICAgICAgICAgICAgICAgbWF4QWdlU2Vjb25kczogMzAgKiAyNCAqIDYwICogNjAsIC8vIDMwIERheXNcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgXSxcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgfSksXG4gICAgXSxcbiAgICBzZXJ2ZXI6IHtcbiAgICAgICAgaG9zdDogJzAuMC4wLjAnLFxuICAgICAgICBobXI6IHtcbiAgICAgICAgICAgIGhvc3Q6IGdldExvY2FsSVAoKSwgXG4gICAgICAgIH0sXG4gICAgfSxcbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUE2WSxTQUFTLG9CQUFvQjtBQUMxYSxPQUFPLGFBQWE7QUFDcEIsT0FBTyxTQUFTO0FBQ2hCLE9BQU8sUUFBUTtBQUNmLFNBQVMsZUFBZTtBQUV4QixTQUFTLGFBQWE7QUFDbEIsUUFBTSxhQUFhLEdBQUcsa0JBQWtCO0FBQ3hDLGFBQVcsUUFBUSxPQUFPLEtBQUssVUFBVSxHQUFHO0FBQzFDLGVBQVcsU0FBUyxXQUFXLElBQUksR0FBRztBQUNwQyxVQUFJLE1BQU0sV0FBVyxVQUFVLENBQUMsTUFBTSxVQUFVO0FBQzlDLGVBQU8sTUFBTTtBQUFBLE1BQ2Y7QUFBQSxJQUNGO0FBQUEsRUFDRjtBQUNBLFNBQU87QUFDVDtBQUVGLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQSxNQUNQLFNBQVM7QUFBQSxJQUNiLENBQUM7QUFBQSxJQUNELElBQUk7QUFBQSxNQUNBLFVBQVU7QUFBQSxRQUNOLG9CQUFvQjtBQUFBLFVBQ2hCLE1BQU07QUFBQSxVQUNOLGlCQUFpQjtBQUFBLFFBQ3JCO0FBQUEsTUFDSjtBQUFBLElBQ0osQ0FBQztBQUFBLElBQ0QsUUFBUTtBQUFBLE1BQ0osY0FBYztBQUFBLE1BQ2QsVUFBVTtBQUFBLFFBQ04sUUFBUTtBQUFBLFFBQ1IsY0FBYztBQUFBLFFBQ2QsZUFBZTtBQUFBLFFBQ2YsYUFBYTtBQUFBLFFBQ2IsV0FBVztBQUFBLFFBQ1gsb0JBQW9CO0FBQUEsUUFDcEIsZUFBZTtBQUFBLFFBQ2YsZUFBZTtBQUFBLFFBQ2YsU0FBUztBQUFBLFVBQ0w7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsWUFDVCxXQUFXO0FBQUEsVUFDZjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFVBQ2I7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsVUFDYjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxVQUNiO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFlBQ1QsV0FBVztBQUFBLFVBQ2Y7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsWUFDVCxXQUFXO0FBQUEsVUFDZjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxZQUNULFdBQVc7QUFBQSxVQUNmO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFlBQ1QsV0FBVztBQUFBLFVBQ2Y7QUFBQSxVQUNBO0FBQUEsWUFDSSxPQUFPO0FBQUEsWUFDUCxRQUFRO0FBQUEsWUFDUixTQUFTO0FBQUEsWUFDVCxXQUFXO0FBQUEsVUFDZjtBQUFBLFVBQ0E7QUFBQSxZQUNJLE9BQU87QUFBQSxZQUNQLFFBQVE7QUFBQSxZQUNSLFNBQVM7QUFBQSxZQUNULFdBQVc7QUFBQSxVQUNmO0FBQUEsVUFDQTtBQUFBLFlBQ0ksT0FBTztBQUFBLFlBQ1AsUUFBUTtBQUFBLFlBQ1IsU0FBUztBQUFBLFlBQ1QsV0FBVztBQUFBLFVBQ2Y7QUFBQSxRQUNKO0FBQUEsTUFDSjtBQUFBLE1BQ0EsU0FBUztBQUFBLFFBQ1AsZ0JBQWdCO0FBQUEsVUFDZDtBQUFBLFlBQ0UsWUFBWTtBQUFBLFlBQ1osU0FBUztBQUFBLFlBQ1QsU0FBUztBQUFBLGNBQ1AsV0FBVztBQUFBLGNBQ1gsWUFBWTtBQUFBLGdCQUNWLFlBQVk7QUFBQSxnQkFDWixlQUFlLEtBQUssS0FBSyxLQUFLO0FBQUE7QUFBQSxjQUNoQztBQUFBLFlBQ0Y7QUFBQSxVQUNGO0FBQUEsUUFDRjtBQUFBLE1BQ0Y7QUFBQSxJQUNGLENBQUM7QUFBQSxFQUNQO0FBQUEsRUFDQSxRQUFRO0FBQUEsSUFDSixNQUFNO0FBQUEsSUFDTixLQUFLO0FBQUEsTUFDRCxNQUFNLFdBQVc7QUFBQSxJQUNyQjtBQUFBLEVBQ0o7QUFDSixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
