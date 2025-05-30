class THeader extends HTMLElement {
  connectedCallback() {
    const pageLinks = this.getAttribute("page-links");
    const sessionLinks = this.getAttribute("session-links")
    const indexLink = this.getAttribute("index-link");
    const isLogged = this.getAttribute("logged") === "true";

    // Solo mostramos el nav si hay sesión iniciada
    const navSection = isLogged
      ? `
        <nav>
          <a href="${pageLinks}vista-clientes.php" class="link-animation">Clientes</a>
          <a href="${pageLinks}vista-muestras.php" class="link-animation">Muestras</a>
          
        </nav>
      `
      : "";

    // <a href="${pageLinks}vista-analisis.php" class="link-animation">Análisis</a>  

    // Botones de autenticación
    const authButtons = isLogged
      ? `
        <div class="auth-buttons">
          <a href="${sessionLinks}cuenta.php" class="btn">Cuenta</a>
          <a href="${sessionLinks}logout.php" class="btn">Cerrar sesión</a>
        </div>
      `
      : `
        <div class="auth-buttons">
          <a href="${sessionLinks}login.php" class="btn">Iniciar sesión</a>
          <a href="${sessionLinks}registro.php" class="btn">Registrarse</a>
        </div>
      `;

    this.innerHTML = `
      <header>
        <div class="header-content">
          <a href="${indexLink}index.php">
            <img class="logo" src="${indexLink}../../static/img/logo-white-square/logo-transparent-svg.svg" />
          </a>
          ${navSection}
          ${authButtons}
        </div>  
      </header>
    `;
  }
}

customElements.define('t-header', THeader);


class TFooter extends HTMLElement {

  connectedCallback(){
    this.innerHTML = `
      <footer>
        <div class="footer">
          <p>
            ©2025 FLASK MANAGER
          </p>
        </div>
      </footer>
    `;
  }

/*   connectedCallback() {
    this.innerHTML = `
      <footer>
        <div class="footer">
          <div class="footer-left">
            <img class="logo" src="img/almost logo.png" />
            <p>
              We build websites that load in under a minute. <br />
              Sometimes even faster.
            </p>
          </div>
          <div class="footer-right">
            <div class="social-media-icons">
              <img src="img/icons8-github.svg" class="social-media" />
              <img src="img/icons8-linkedin.svg" class="social-media" />
              <img src="img/icons8-x.svg" class="social-media" />
              <img src="img/icons8-youtube.svg" class="social-media" />
            </div>
            <p>
              3 Way, 69th Street, Somewhere, USB <br />
              123-456-7890 <br />
              bye@almost.dev
            </p>
          </div>
        </div>
      </footer>
    `;
  } */
}
customElements.define("t-footer", TFooter);

// hamburger
/* const ham = document.querySelector(".ham");
const nav = document.querySelector("nav");
ham.addEventListener("click", toggle);
nav.addEventListener("click", toggle);
function toggle() {
  ham.src = ham.src.includes("img/ham-close.svg")
    ? "img/ham-open.svg"
    : "img/ham-close.svg";
  nav.classList.toggle("show");
} */
