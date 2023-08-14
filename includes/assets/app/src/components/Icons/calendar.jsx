const Calendar = props => (
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width={24}
        height={24}
        viewBox={"0 0 24 24"}
        fill="none"
        stroke="currentColor"
        strokeLinecap="round"
        strokeLinejoin="round"
        className="-icon -calendar"
        {...props}
    >
        <rect width={18} height={18} x={3} y={4} rx={2} ry={2} />
        <path d="M16 2v4M8 2v4M3 10h18" />
    </svg>
)

export default Calendar;
