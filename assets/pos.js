(() => {
  const products = [...document.querySelectorAll('.product')];
  const cart = new Map();
  const search = document.getElementById('productSearch');
  const catFilter = document.getElementById('catFilter');
  const discount = document.getElementById('discount');
  const paid = document.getElementById('paid');
  const fmt = n => '₱' + (n / 100).toLocaleString('en-PH', {minimumFractionDigits:2,maximumFractionDigits:2});
  const cents = value => Math.round((Number(value)||0)*100);
  const safe = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const render = () => {
    const entries = [...cart.values()];
    const count=document.getElementById('orderCount');if(count)count.textContent=entries.reduce((n,p)=>n+p.qty,0)+' items';
    document.getElementById('cartItems').innerHTML = entries.length ? entries.map(p => `<div class="cart-line"><div class="cart-line-info"><strong>${safe(p.name)}</strong><small>${fmt(p.price)} each</small></div><div class="qty-controls"><button type="button" data-edit="${p.id}" data-delta="-1" aria-label="Remove one">−</button><span>${p.qty}</span><button type="button" data-edit="${p.id}" data-delta="1" aria-label="Add one">+</button></div><b>${fmt(p.price*p.qty)}</b></div>`).join('') : '<p class="muted">Your cart is empty.</p>';
    const subtotal = entries.reduce((sum,p) => sum + p.price*p.qty, 0);
    const total = Math.max(0, subtotal-cents(discount.value));
    const change = Math.max(0,cents(paid.value)-total);
    document.getElementById('subtotal').textContent = fmt(subtotal);
    document.getElementById('total').textContent = fmt(total);
    document.getElementById('change').textContent = fmt(change);
    document.getElementById('cartInput').value = JSON.stringify(entries.map(p => ({id:p.id,qty:p.qty})));
    document.getElementById('checkoutBtn').disabled = !entries.length || cents(discount.value)>subtotal || cents(paid.value)<total;
  };
  for (const el of products) el.addEventListener('click', () => {
    const id=Number(el.dataset.id), stock=Number(el.dataset.stock);
    const old=cart.get(id);
    if (old) {if(old.qty<stock)old.qty++;}
    else cart.set(id,{id,name:el.dataset.name,price:cents(el.dataset.price),stock,qty:1});
    render();
  });
  document.getElementById('cartItems').addEventListener('click', event => {
    const btn=event.target.closest('button[data-edit]');if(!btn)return;
    const p=cart.get(Number(btn.dataset.edit));if(!p)return;
    p.qty=Math.min(p.stock,p.qty+Number(btn.dataset.delta));if(p.qty<1)cart.delete(p.id);render();
  });
  const filter = () => {const q=search.value.trim().toLowerCase(),cat=catFilter.value;let shown=0;for(const p of products){p.hidden=!(p.dataset.name.toLowerCase().includes(q)||p.dataset.sku.toLowerCase().includes(q))||(cat!==''&&p.dataset.category!==cat);if(!p.hidden)shown++;}const empty=document.getElementById('noResults');if(empty)empty.hidden=shown!==0||products.length===0;document.querySelectorAll('.category-chip').forEach(b=>b.classList.toggle('selected',b.dataset.category===cat));};
  document.querySelectorAll('.category-chip').forEach(b=>b.addEventListener('click',()=>{catFilter.value=b.dataset.category;filter();}));
  search.addEventListener('input',filter);catFilter.addEventListener('change',filter);
  discount.addEventListener('input',render);paid.addEventListener('input',render);
  document.getElementById('checkoutForm').addEventListener('submit',event => {render();if(document.getElementById('checkoutBtn').disabled)event.preventDefault();});
  render();
})();
