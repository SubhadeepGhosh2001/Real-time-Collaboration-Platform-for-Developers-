import { Link } from "react-router-dom";

const Navbar = () => (
  <nav
    style={{
      position: "fixed", // Keep navbar fixed at the top-right
      top: "1.5rem",
      right: "2rem",
      zIndex: 100,
      background: "#1e293b",
      padding: "1rem 2rem",
      borderRadius: "25px",
      display: "flex",
      gap: "1.5rem",
      alignItems: "center",
      boxShadow: "0 2px 20px rgba(0,0,0,0.15)",
    }}
  >
    <Link
      to="http://localhost/code-sync/index.php"
      style={{
        color: "#cbd5e1",
        marginRight: "1.5rem",
        textDecoration: "none",
        fontWeight: 500,
        fontSize: "1.1rem",
      }}
    >
      Home
    </Link>
    <a
      href="http://localhost/code-sync/logout.php"
      style={{
        color: "#ff6b6b",
        textDecoration: "none",
        fontWeight: 600,
        fontSize: "1.1rem",
      }}
    >
      Logout
    </a>
  </nav>
);

export default Navbar;