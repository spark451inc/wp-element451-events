const Clock = props => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width={24}
        height={24}
        viewBox={"0 0 24 24"}
        fill="none"
        stroke="currentColor"
        strokeLinecap="round"
        strokeLinejoin="round"
        className="-icon -clock"
        {...props}
    >
        <circle cx={12} cy={12} r={10} />
        <path d="M12 6v6l4 2" />
    </svg>
)

export default Clock;
