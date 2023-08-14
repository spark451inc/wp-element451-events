const debounce = (cb, delay) => {
  let timeout;

  return (...args) => {
    let context = this;

    clearTimeout(timeout);
    timeout = setTimeout(() => cb.apply(context, args), delay);
  }
}

export default debounce;
