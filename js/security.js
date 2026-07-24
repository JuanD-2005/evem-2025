// js/security.js - Utilidades de seguridad para el frontend
//
// escapeHTML() debe aplicarse a CUALQUIER dato que venga del backend (o del
// usuario) justo antes de insertarlo en un template literal destinado a
// innerHTML/outerHTML/insertAdjacentHTML, o al option `html` de SweetAlert2.
// No sirve para insertar datos dentro de atributos tipo onclick="...": eso
// hay que evitarlo por completo (ver admin-evem.html / admin-dim.html /
// admin-festival.html), porque el navegador decodifica entidades HTML antes
// de interpretar el atributo como JS, así que el escape no protege ahí.
function escapeHTML(value) {
  if (value === null || value === undefined) return "";
  return String(value).replace(/[&<>"']/g, (char) => {
    switch (char) {
      case "&": return "&amp;";
      case "<": return "&lt;";
      case ">": return "&gt;";
      case '"': return "&quot;";
      case "'": return "&#39;";
      default: return char;
    }
  });
}
