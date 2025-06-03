class THeader extends HTMLElement {
  connectedCallback() {
    const pageLinks = this.getAttribute("page-links");
    const sessionLinks = this.getAttribute("session-links")
    const indexLink = this.getAttribute("index-link");
    const isLogged = this.getAttribute("logged") === "true";

    // Solo mostramos el nav si hay sesión iniciada
    const navSection = isLogged
      ? `
        <nav class="nav-links">
          <a href="${pageLinks}vista-clientes.php" class="link-animation">Clientes</a>
          <a href="${pageLinks}vista-muestras.php" class="link-animation">Muestras</a>
          
        </nav>
      `
      : "";

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
          <div class="header-left">
            <a href="${indexLink}index.php">
              <img class="logo" src="${indexLink}../../static/img/logo-v2/logo-transparent-2-png.png" />
            </a>
            ${navSection}
          </div>
          <div class="header-right">
            ${authButtons}
          </div> 
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
}
customElements.define("t-footer", TFooter);