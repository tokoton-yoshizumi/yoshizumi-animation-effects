function hideYaeLoader() {
  const loader = document.getElementById("yae-loader");
  if (loader) {
    loader.style.transition = "opacity 0.5s";
    loader.style.opacity = "0";
    setTimeout(() => loader.remove(), 600);
  }

  const gradient = document.querySelector(".yae-gradient-loader");
  if (gradient) {
    gradient.classList.add("fade-out");
    setTimeout(() => gradient.remove(), 600);
  }
}

jQuery(document).ready(function () {
  console.log("globalLoaderEnabled:", yaeSettings.globalLoaderEnabled);

  if (!yaeSettings.globalLoaderEnabled) return;

  const speedConfig = {
    fast: { step: 4, delay: 20, duration: 500 },
    normal: { step: 3, delay: 25, duration: 1200 },
    slow: { step: 2, delay: 40, duration: 2000 },
  };

  const type = yaeSettings.loaderType || "spinner";
  const speed = yaeSettings.loaderSpeed || "normal";
  const config = speedConfig[speed] || speedConfig.normal;

  if (type === "gradient") {
    // ▼ プリセット色の定義
    const presets = {
      bluegreen: ["#0073aa", "#43a047"],
      redorange: ["#d32f2f", "#fdd835"],
      purplepink: ["#8e24aa", "#ec407a"],
    };

    // ▼ 初期値（カスタムにフォールバック）
    let color1 = yaeSettings.gradientColor1 || "#0073aa";
    let color2 = yaeSettings.gradientColor2 || "#43a047";

    // ▼ プリセットが選ばれている場合はプリセットの色を使用
    if (yaeSettings.gradientColorOption !== "custom") {
      const preset = presets[yaeSettings.gradientColorOption];
      if (preset) {
        color1 = preset[0];
        color2 = preset[1];
      }
    }

    const loader = document.createElement("div");
    loader.className = "yae-gradient-loader";

    loader.style.setProperty("--gradient-color-1", color1);
    loader.style.setProperty("--gradient-color-2", color2);

    // ▼ ロゴ画像の表示（設定されていれば）
    const logoUrl = yaeSettings.logoImage;
    if (logoUrl) {
      const logo = document.createElement("img");
      logo.src = logoUrl;

      const sizeOption = yaeSettings.logoSizeOption || "medium";
      const logoWidth =
        sizeOption === "custom"
          ? yaeSettings.logoWidth || 200
          : { small: 180, medium: 240, large: 300 }[sizeOption] || 240;

      logo.style.maxWidth = `${logoWidth}px`;
      logo.className = "yae-logo";
      loader.appendChild(logo);
    }

    const displayTime = config.duration;

    document.body.appendChild(loader);

    jQuery(window).on("load", function () {
      setTimeout(hideYaeLoader, displayTime);
    });
  }

  if (type === "progress") {
    let progress = 0;

    const interval = setInterval(function () {
      progress += Math.floor(Math.random() * config.step) + 1;
      if (progress > 100) progress = 100;

      jQuery(".yae-progress-bar").css("width", progress + "%");
      jQuery(".yae-percentage").text(progress + "%");

      if (progress >= 100) {
        clearInterval(interval);
        setTimeout(function () {
          jQuery("#yae-loader").fadeOut(500);
        }, 200);
      }
    }, config.delay);
  } else if (type === "dots" || type === "spinner" || type === "logo") {
    if (type === "dots") {
      const colorPresets = {
        black: "#333333",
        blue: "#0073aa",
        red: "#e53935",
        green: "#43a047",
      };
      const color =
        yaeSettings.dotsColorOption === "custom"
          ? yaeSettings.dotsColor || "#333333"
          : colorPresets[yaeSettings.dotsColorOption] || "#333333";

      const sizePresets = {
        small: 14,
        medium: 18,
        large: 22,
      };
      const size =
        yaeSettings.dotsSizeOption === "custom"
          ? yaeSettings.dotsSize || 14
          : sizePresets[yaeSettings.dotsSizeOption] || 14;

      const dotsContainer = document.querySelector(".yae-dots");
      if (dotsContainer) {
        dotsContainer.style.setProperty("--dot-color", color);
        dotsContainer.style.setProperty("--dot-size", `${size}px`);
      }
    }

    const startTime = Date.now();

    jQuery(window).on("load", function () {
      const elapsed = Date.now() - startTime;
      const remaining = Math.max(config.duration - elapsed, 0);

      setTimeout(hideYaeLoader, remaining);
    });
  }

  // ▼ AOS の初期化（AOSが読み込まれていれば）
  if (typeof AOS !== "undefined") {
    AOS.init({
      once: true, // 1回だけ再生（必要に応じて false）
      duration: 800, // アニメーション時間（ms）
      offset: 100, // 発火タイミングの距離（px）
      delay: 0, // 全体の遅延（data-aos-delayでも個別制御可）
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  if (!yaeSettings.globalLoaderEnabled) return;

  // speedConfig を使用して duration を決定
  const speed = yaeSettings.loaderSpeed || "normal";
  const speedConfig = {
    fast: { duration: 500 },
    normal: { duration: 1200 },
    slow: { duration: 2000 },
  };
  const config = speedConfig[speed] || speedConfig.normal;

  // 保険としてローダーが残っていれば非表示にする
  setTimeout(() => {
    const stillExists =
      document.getElementById("yae-loader") ||
      document.querySelector(".yae-gradient-loader");
    if (stillExists) {
      hideYaeLoader();
    }
  }, config.duration);

  if (yaeSettings.loaderType !== "transition") return;

  const overlay = document.querySelector(".yae-slide-overlay");
  if (!overlay) return;

  // ▼ オーバーレイカラー設定
  let overlayColor = "";
  if (yaeSettings.transitionOverlayColorOption === "custom") {
    overlayColor = yaeSettings.transitionOverlayColor || "#111111"; // カスタムカラー
  } else {
    const colorPresets = {
      black: "#111111",
      blue: "#0073aa",
      red: "#e53935",
      green: "#43a047",
    };
    overlayColor =
      colorPresets[yaeSettings.transitionOverlayColorOption] || "#111111"; // プリセットカラー
  }

  // 透明度の設定（0~100）を反映
  const opacity = yaeSettings.transitionOverlayOpacity / 100 || 1.0; // デフォルト値 100%
  overlay.style.setProperty(
    "--overlay-color",
    `${overlayColor}${Math.round(opacity * 255)
      .toString(16)
      .padStart(2, "0")}`
  );

  // スライドアウトが有効なら、ページ表示直後に active を設定
  if (yaeSettings.transitionSlideOut === "1") {
    overlay.style.display = "block";
    overlay.classList.add("active");
  } else {
    overlay.style.display = "none";
  }

  // ページ完全読み込み時にスライドアウト（active を外す）
  window.addEventListener("load", function () {
    if (yaeSettings.transitionSlideOut === "1") {
      overlay.classList.remove("active");
      setTimeout(() => {
        overlay.style.display = "none";
      }, 500);
    }
  });

  if (yaeSettings.transitionDiagonal === "1") {
    overlay.classList.add("diagonal");
  } else {
    overlay.classList.remove("diagonal");
  }

  // リンククリック時（スライドイン）
  document
    .querySelectorAll('a:not([target="_blank"]):not([href^="#"])')
    .forEach((link) => {
      link.addEventListener("click", function (e) {
        const href = this.getAttribute("href");

        if (
          !href ||
          href.startsWith("javascript:") ||
          href === "#" ||
          !href.includes(location.host)
        ) {
          return;
        }

        e.preventDefault();

        overlay.style.display = "block";
        void overlay.offsetWidth; // reflow
        overlay.classList.add("active");

        setTimeout(() => {
          window.location.href = href;
        }, 500);
      });
    });
});
